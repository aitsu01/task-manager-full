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

    /**
     * Lista task del progetto
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $this->taskService
            ->getPaginatedTasks($project);

        $tasks->load('assignedUser');

    

$tasks->load(['assignedUser', 'project']);

        return TaskResource::collection($tasks);
    }

    /**
     * Creazione task (TUTTI i membri possono farlo)
     */
    public function store(Request $request, Project $project)
    {
        // Basta essere membro
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
     * Aggiorna task
     * - Owner/Admin → tutto
     * - Member → solo se assegnata a lui
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $user = auth()->user();

        $isOwner = $project->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();

        $isAdmin = $user->role && $user->role->name === 'admin';

        // Se NON owner e NON admin
        if (!$isOwner && !$isAdmin) {

            // Può modificare solo se task assegnata a lui
            if ($task->assigned_user_id !== $user->id) {
                abort(403, 'Non puoi modificare questa task');
            }
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['todo', 'doing', 'done'])],
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);

        $task = $this->taskService
            ->updateTask($task, $validated);

        $task->load(['project', 'assignedUser']);

        return new TaskResource($task);
    }

    /**
     * Eliminazione task
     * Solo Owner o Admin
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if (!$this->taskService->belongsToProject($task, $project)) {
            abort(404);
        }

        $user = auth()->user();

        $isOwner = $project->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();

        $isAdmin = $user->role && $user->role->name === 'admin';

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Solo owner o admin possono eliminare task');
        }

        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}