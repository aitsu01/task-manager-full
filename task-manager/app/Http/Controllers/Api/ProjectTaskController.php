<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use App\Services\TaskService;

class ProjectTaskController extends Controller
{
    use AuthorizesRequests;

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $this->taskService
            ->getPaginatedTasks($project);

        return TaskResource::collection($tasks);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['todo', 'doing', 'done'])],
            'due_date' => 'nullable|date'
        ]);

        $task = $this->taskService
            ->createTask($project, $validated);

        return new TaskResource($task);
    }

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        return new TaskResource($task);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['todo', 'doing', 'done'])],
            'due_date' => 'nullable|date'
        ]);

        $task = $this->taskService
            ->updateTask($task, $validated);

        return new TaskResource($task);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}