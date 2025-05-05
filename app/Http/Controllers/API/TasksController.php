<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskFormRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->user()?->cannot('view', Task::class)) {
            abort(403, 'Forbidden');
        }

        // Only get tasks belonging to the authenticated user
        $tasks = Task::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        
        return response()->json([
            'data' => $tasks
        ]);
    }

    public function store(TaskFormRequest $request): JsonResponse
    {
        if ($request->user()?->cannot('create', Task::class)) {
            abort(403, 'Forbidden');
        }

        $task = new Task($request->validated());
        $task->user_id = $request->user()->id;
        $task->save();
        
        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], Response::HTTP_CREATED);
    }

    public function update(TaskFormRequest $request, Task $task): JsonResponse
    {
        if ($request->user()?->cannot('update', $task)) {
            abort(403, 'Forbidden');
        }
        
        $task->update($request->validated());
        
        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($request->user()?->cannot('delete', $task)) {
            abort(403, 'Forbidden');
        }
        
        $task->delete();
        
        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
