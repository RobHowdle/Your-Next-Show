<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Services\TodoService;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    protected $todoService;

    protected function getUserId()
    {
        return Auth::id();
    }

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function showTodos($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);
        $modules = collect(session('modules', []));

        $todoItems = $this->todoService->getUncompletedTodos($user, $dashboardType);

        return view('admin.dashboards.todo-list', [
            'todoItems' => $todoItems,
            'modules' => $modules,
            'dashboardType' => $dashboardType,
            'userId' => Auth::id()
        ]);
    }

    // public function getTodos($dashboardType, Request $request)
    // {
    //     $user = Auth::user()->load(['promoters', 'venues', 'todos', 'otherService']);
    //     $perPage = 6;
    //     $page = $request->input('page', 1);
    //     $todoItems = collect();

    //     switch ($dashboardType) {
    //         case 'promoter':
    //             $promoterCompany = $user->promoters;
    //             $serviceableId = $promoterCompany->pluck('id');

    //             if ($promoterCompany->isEmpty()) {
    //                 return response()->json([
    //                     'view' => view('components.todo-items', ['todoItems' => collect()])->render(),
    //                     'hasMore' => false,
    //                 ]);
    //             }

    //             $todoItems = Todo::where('serviceable_type', Promoter::class)
    //                 ->whereIn('serviceable_id', $serviceableId)
    //                 ->where('completed', false)
    //                 ->orderBy('created_at', 'DESC')
    //                 ->paginate($perPage);

    //             break;

    //         case 'artist':
    //             $bandServices = $user->otherService("Artist");
    //             $serviceableId = $bandServices->pluck('other_services.id');

    //             if (!$bandServices) {
    //                 return response()->json([
    //                     'view' => view('components.todo-items', ['todoItems' => collect()])->render(),
    //                     'hasMore' => false,
    //                 ]);
    //             }

    //             $todoItems = Todo::where('serviceable_type', OtherService::class)
    //                 ->whereIn('serviceable_id', $serviceableId)
    //                 ->where('completed', false)
    //                 ->orderBy('created_at', 'DESC')
    //                 ->paginate($perPage);

    //             break;

    //             // case 'designer':
    //             //     $designerCompanies = $user->designers;
    //             //     $serviceableId = $designerCompanies->pluck('id');

    //             //     if ($designerCompanies->isEmpty()) {
    //             //         return response()->json([
    //             //             'view' => view('components.todo-items', ['todoItems' => collect()])->render(),
    //             //             'hasMore' => false,
    //             //         ]);
    //             //     }

    //             //     $todoItems = Todo::where('serviceable_type', Designer::class)
    //             //         ->whereIn('serviceable_id', $serviceableId)
    //             //         ->where('completed', false)
    //             //         ->orderBy('created_at', 'DESC')
    //             //         ->paginate($perPage);

    //             //     break;

    //         default:
    //             return response()->json([
    //                 'view' => view('components.todo-items', ['todoItems' => collect()])->render(),
    //                 'hasMore' => false,
    //             ]);
    //     }

    //     return response()->json([
    //         'view' => view('components.todo-items', compact('todoItems'))->render(),
    //         'hasMore' => $todoItems->hasMorePages(),
    //     ]);
    // }

    public function newTodoItem($dashboardType, Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'task' => 'required|string'
        ]);

        $servicealeableType = null;
        $serviceableId = null;

        if ($dashboardType === 'promoter') {
            $servicealeableType = Promoter::class;
            $serviceableId = $user->promoters->first()->id;
        } elseif ($dashboardType === 'artist') {
            $servicealeableType = OtherService::class;
            $serviceableId = $user->otherService('Artist')->first()->id;
        } elseif ($dashboardType === 'designer') {
            $servicealeableType = OtherService::class;
            $serviceableId = $user->otherService('Designer')->first()->id;
        } elseif ($dashboardType === 'photographer') {
            $servicealeableType = OtherService::class;
            $serviceableId = $user->otherService('Photographer')->first()->id;
        } elseif ($dashboardType === 'videographer') {
            $servicealeableType = OtherService::class;
            $serviceableId = $user->otherService('Videographer')->first()->id;
        } elseif ($dashboardType === 'venue') {
            $servicealeableType = OtherService::class;
            $serviceableId = $user->otherService('Venue')->first()->id;
        } else {
            $servicealeableType = null;
            $serviceableId = null;
        }

        $todoItem = Todo::create([
            'user_id' => $user->id,
            'serviceable_id' => $serviceableId,
            'serviceable_type' => $servicealeableType,
            'item' => $request->task,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item Added Successfully',
            'todoItem' => $todoItem,
        ]);
    }

    public function completeTodoItem($dashboardType, $id)
    {

        $todoItem = Todo::findOrFail($id);

        $todoItem->completed = true;
        $todoItem->completed_at = now();
        $todoItem->save();

        // Return a success response
        return response()->json([
            'message' => 'Todo item marked as completed!',
            'todoItem' => $todoItem,
        ]);
    }

    public function deleteTodoItem($dashboardType, $id)
    {
        try {
            $todoItem = Todo::findOrFail($id);
            $todoItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Todo item deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete todo item'
            ], 500);
        }
    }

    public function hasCompletedTodos($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);
        $hasCompleted = $this->todoService->hasCompletedTodos($user, $dashboardType);

        return response()->json([
            'hasCompleted' => $hasCompleted
        ]);
    }

    public function showCompletedTodoItems($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);

        try {
            $todoItems = $this->todoService->getTodosByType(
                $user,
                $dashboardType,
                6,  // perPage
                1,  // first page
                true // completed flag
            );

            return response()->json([
                'success' => true,
                'html' => view('components.todo-items', ['todoItems' => $todoItems])->render(),
                'hasMorePages' => $todoItems->hasMorePages(),
                'hasCompleted' => $todoItems->isNotEmpty()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load completed items',
                'html' => view('components.todo-items', ['todoItems' => collect()])->render()
            ], 500);
        }
    }

    public function showUncompletedTodoItems($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);

        try {
            $todoItems = $this->todoService->getTodosByType(
                $user,
                $dashboardType,
                6,  // perPage
                1,  // first page
                false // uncompleted flag
            );

            return response()->json([
                'success' => true,
                'html' => view('components.todo-items', ['todoItems' => $todoItems])->render(),
                'hasMorePages' => $todoItems->hasMorePages(),
                'hasCompleted' => $todoItems->isNotEmpty()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load completed items',
                'html' => view('components.todo-items', ['todoItems' => collect()])->render()
            ], 500);
        }
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

    public function loadMoreTodos($dashboardType, Request $request)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);
        $todoItems = $this->todoService->getTodosByType(
            $user,
            $dashboardType,
            6,
            $request->input('page', 1)
        );

        return response()->json([
            'html' => view('components.todo-items', ['todoItems' => $todoItems])->render(),
            'hasMorePages' => $todoItems->hasMorePages()
        ]);
    }

    public function hasUncompletedTodos($dashboardType)
    {
        $user = Auth::user()->load(['promoters', 'todos', 'otherService']);
        $hasUncompleted = $this->todoService->hasUncompletedTodos($user, $dashboardType);

        return response()->json([
            'hasUncompleted' => $hasUncompleted
        ]);
    }
}