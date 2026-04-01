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
use Illuminate\Support\Facades\Notification;

class ProjectTaskController extends Controller
{
    use AuthorizesRequests;

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Lista task del progetto
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $this->taskService
            ->getPaginatedTasks($project);

        $tasks->load(['assignedUser', 'project']);

        return TaskResource::collection($tasks);
    }

    /**
     * Creazione task (TUTTI i membri)
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['todo', 'doing', 'done'])],
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        $task = $this->taskService
            ->createTask($project, $validated);

        $task->load(['project', 'assignedUser']);

        return new TaskResource($task);
    }

    /**
     * Mostra singola task
     */
    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $task->load('assignedUser');

        return new TaskResource($task);
    }

    /**
     * Update task (policy)
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['todo', 'doing', 'done'])],
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        $task = $this->taskService->updateTask($task, $validated);

        $task->load(['project', 'assignedUser']);

        return new TaskResource($task);
    }

    /**
     * Delete task (solo owner/admin)
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

public function updateStatus(Request $request, Project $project, Task $task)
{
    $this->authorize('view', $project);

    if (!$this->taskService->belongsToProject($task, $project)) {
        abort(404);
    }

    $this->authorize('updateStatus', $task);

    $request->validate([
        'status' => 'required|in:todo,doing,done'
    ]);

    // 🔥 SALVA STATO VECCHIO
    $oldStatus = $task->status;

    // 🔥 AGGIORNA
    $task->status = $request->status;
    $task->save();

    // 🔥 NOTIFICHE
    $members = $project->users()->get();

    foreach ($members as $member) {
        $member->notify(
            new \App\Notifications\TaskStatusUpdatedNotification($task, $oldStatus)
        );
    }

    return response()->json([
        'message' => 'Status aggiornato',
        'task' => $task
    ]);
}
}