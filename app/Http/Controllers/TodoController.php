<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    protected function getService($dashboardType)
    {
        $user = Auth::user();

        return match ($dashboardType) {
            'promoter' => $user->promoters()->first(),
            'venue' => $user->venues()->first(),
            'artist', 'designer', 'photographer', 'videographer' =>
            $user->otherService(ucfirst($dashboardType))->first(),
            default => abort(404, 'Invalid dashboard type'),
        };
    }

    public function index($dashboardType)
    {
        $user = Auth::user();
        $service = $this->getService($dashboardType);
        $modules = collect(session('modules', []));
        $userId = $this->getUserId();

        $todoItems = Todo::where('user_id', $user->id)
            ->where('serviceable_type', get_class($service))
            ->where('serviceable_id', $service->id)
            ->where('completed', false)
            ->orderByDesc('created_at')
            ->paginate(6);

        $hasCompleted = Todo::where('user_id', $user->id)
            ->where('serviceable_type', get_class($service))
            ->where('serviceable_id', $service->id)
            ->where('completed', true)
            ->exists();

        $totalItems = Todo::where('user_id', $user->id)
            ->where('serviceable_type', get_class($service))
            ->where('serviceable_id', $service->id)
            ->where('completed', false)
            ->count();

        $hasMorePages = $todoItems->hasMorePages();

        return view('admin.dashboards.todo-list', compact(
            'userId',
            'todoItems',
            'hasCompleted',
            'hasMorePages',
            'dashboardType',
            'modules'
        ));
    }

    public function getCounts(Request $request, $dashboardType)
    {
        $service = $this->getService($dashboardType);

        $counts = [
            'hasCompleted' => Todo::where('serviceable_type', get_class($service))
                ->where('serviceable_id', $service->id)
                ->where('completed', true)
                ->exists(),
            'hasUncompleted' => Todo::where('serviceable_type', get_class($service))
                ->where('serviceable_id', $service->id)
                ->where('completed', false)
                ->exists(),
            'total' => Todo::where('serviceable_type', get_class($service))
                ->where('serviceable_id', $service->id)
                ->count(),
        ];

        $counts['hasMorePages'] = $counts['total'] > 6; // 6 items per page

        return response()->json($counts);
    }

    public function store(Request $request, $dashboardType)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:500',
            'due_date' => 'nullable|date'
        ]);

        $user = Auth::user();
        $service = $this->getService($dashboardType);

        $todo = new Todo([
            'item' => $validated['task'],
            'due_date' => $validated['due_date'] ?? null,
            'completed' => false,
            'user_id' => $user->id
        ]);

        $todo->serviceable()->associate($service);
        $todo->save();

        return response()->json([
            'success' => true,
            'todo' => $todo
        ]);
    }

    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $todoItems = Todo::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($todoItems->isEmpty()) {
            return response()->json([
                'html' => '',
                'hasMorePages' => false
            ]);
        }

        $html = '';
        foreach ($todoItems as $todo) {
            $html .= view('partials.todo-item', ['todo' => $todo])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $todoItems->hasMorePages(),
            'nextPage' => $todoItems->currentPage() + 1
        ]);
    }

    public function complete(Request $request, $dashboardType, $id)
    {
        $service = $this->getService($dashboardType);

        $todo = Todo::where('serviceable_type', get_class($service))
            ->where('serviceable_id', $service->id)
            ->findOrFail($id);

        $todo->completed = true;
        $todo->save();

        return response()->json(['success' => true]);
    }

    public function destroy($dashboardType, $id)
    {
        $service = $this->getService($dashboardType);

        $todo = Todo::where('serviceable_type', get_class($service))
            ->where('serviceable_id', $service->id)
            ->findOrFail($id);

        $todo->delete();

        return response()->json(['success' => true]);
    }
}