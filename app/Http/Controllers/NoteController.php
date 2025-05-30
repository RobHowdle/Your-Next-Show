<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Services\TodoService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUpdateNoteRequest;

class NoteController extends Controller
{
    protected $todoService;

    protected function getUserId()
    {
        return Auth::id();
    }

    public function getNotes($dashboardType, Request $request)
    {
        try {
            $user = Auth::user()->load(['promoters', 'venues', 'otherService']);
            $perPage = 6;
            $page = $request->input('page', 1);
            $completed = $request->boolean('completed');
            $isAjax = $request->ajax();
            $notes = collect();

            switch ($dashboardType) {
                case 'promoter':
                    $serviceableType = Promoter::class;
                    $serviceableIds = $user->promoters->pluck('id');
                    break;

                case 'venue':
                    $serviceableType = Venue::class;
                    $serviceableIds = $user->venues->pluck('id');
                    break;

                case 'artist':
                case 'designer':
                case 'photographer':
                case 'videographer':
                    $serviceableType = OtherService::class;
                    $serviceableIds = $user->otherService(ucfirst($dashboardType))->pluck('other_services.id');
                    break;

                default:
                    return $this->emptyResponse($isAjax);
            }

            // Return empty response if no services found
            if ($serviceableIds->isEmpty()) {
                return $this->emptyResponse($isAjax);
            }

            // Build the query
            $notes = Note::where('serviceable_type', $serviceableType)
                ->whereIn('serviceable_id', $serviceableIds)
                ->where('completed', $completed)
                ->orderBy('created_at', 'DESC')
                ->paginate($perPage);

            if ($isAjax) {
                return response()->json([
                    'view' => view('components.note-items', compact('notes'))->render(),
                    'hasMore' => $notes->hasMorePages(),
                ]);
            }

            return view('admin.dashboards.show-notes', [
                'userId' => Auth::id(),
                'dashboardType' => $dashboardType,
                'modules' => collect(session('modules', [])),
                'notes' => $notes,
                'title' => ucfirst($dashboardType) . ' Notes'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading notes: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load notes',
                    'message' => $e->getMessage()
                ], 500);
            }

            abort(500, 'Error loading notes');
        }
    }

    private function emptyResponse($isAjax)
    {
        if (!$isAjax) {
            return view('admin.dashboards.show-notes', [
                'notes' => collect(),
                'modules' => collect(session('modules', [])),
                'title' => 'Notes'
            ]);
        }

        return response()->json([
            'view' => '',
            'hasMore' => false,
        ]);
    }

    public function newNoteItem($dashboardType, StoreUpdateNoteRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $serviceableType = null;
        $serviceableId = null;

        switch ($dashboardType) {
            case 'promoter':
                $serviceableType = Promoter::class;
                $promoter = $user->promoters->first();
                if (!$promoter) {
                    return response()->json(['message' => 'No promoter found'], 404);
                }
                $serviceableId = $promoter->id;
                break;

            case 'venue':
                $serviceableType = Venue::class;
                $venue = $user->venues->first();
                if (!$venue) {
                    return response()->json(['message' => 'No venue found'], 404);
                }
                $serviceableId = $venue->id;
                break;

            case 'artist':
            case 'designer':
            case 'photographer':
            case 'videographer':
                $serviceableType = OtherService::class;
                $service = $user->otherService(ucfirst($dashboardType))->first();
                if (!$service) {
                    return response()->json(['message' => "No {$dashboardType} service found"], 404);
                }
                $serviceableId = $service->id;
                break;

            default:
                return response()->json(['message' => 'Invalid dashboard type'], 400);
        }

        $noteItem = Note::create([
            'serviceable_id' => $serviceableId,
            'serviceable_type' => $serviceableType,
            'name' => $validated['name'],
            'text' => $validated['text'],
            'date' => $validated['date'],
            'is_todo' => $validated['is_todo'] ?? false,
        ]);

        if ($noteItem->is_todo) {
            $this->todoService->createTodoFromNote($noteItem);
        }

        return response()->json([
            'message' => 'Note Added Successfully',
            'noteItem' => $noteItem,
        ]);
    }

    public function completeNoteItem($dashboardType, $id)
    {
        $note = Note::findOrFail($id);

        $note->completed = true;
        $note->completed_at = now();
        $note->save();

        return response()->json([
            'message' => 'Note marked as completed!',
            'note' => $note,
        ]);
    }

    public function uncompleteNoteItem($dashboardType, $id)
    {
        $noteItem = Note::findOrFail($id);
        $noteItem->completed = false;
        $noteItem->completed_at = null;
        $noteItem->save();

        return response()->json([
            'message' => 'Todo item marked as uncompleted!',
            'noteItem' => $noteItem,
        ]);
    }

    public function showCompletedNoteItems($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'notes', 'otherService']);
        $perPage = 6;
        $notes = collect();
        $serviceableId = collect();

        switch ($dashboardType) {
            case 'promoter':
                $promoterCompany = $user->promoters;
                $serviceableId = $promoterCompany->pluck('id');

                if ($promoterCompany->isEmpty()) {
                    return response()->json([
                        'view' => view('components.note-items', ['notes' => collect()])->render(),
                        'hasMore' => false,
                    ]);
                }

                // Retrieve completed todo items for the promoter
                $notes = Note::where('serviceable_type', Promoter::class)
                    ->whereIn('serviceable_id', $serviceableId)
                    ->where('completed', true)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
                break;

            case 'artist':
                $bandServices = $user->otherService("Artist");
                $serviceableId = $bandServices->pluck('other_services.id');

                if (!$bandServices) {
                    return response()->json([
                        'view' => view('components.note-items', ['notes' => collect()])->render(),
                        'hasMore' => false,
                    ]);
                }

                // Retrieve completed todo items for the band
                $notes = Note::where('serviceable_type', OtherService::class)
                    ->whereIn('serviceable_id', $serviceableId)
                    ->where('completed', true)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
                break;

            default:
                return response()->json([
                    'view' => view('components.note-items', ['notes' => collect()])->render(),
                    'hasMore' => false,
                ]);
        }

        return response()->json([
            'view' => view('components.note-items', ['notes' => $notes])->render(),
            'hasMore' => $notes->hasMorePages(),
        ]);
    }

    public function uncompleteTodoItem($dashboardType, $id)
    {
        // Find the todo item by ID
        $todoItem = Todo::findOrFail($id);

        // Mark the item as completed
        $todoItem->completed = false;
        $todoItem->completed_at = null;
        $todoItem->save();

        // Return a success response
        return response()->json([
            'message' => 'Todo item marked as uncompleted!',
            'todoItem' => $todoItem,
        ]);
    }

    public function deleteNoteItem($dashboardType, $id)
    {
        $noteItem = Note::findOrFail($id);
        $noteItem->delete();

        return response()->json([
            'message' => 'Note deleted successfully!',
        ]);
    }
}