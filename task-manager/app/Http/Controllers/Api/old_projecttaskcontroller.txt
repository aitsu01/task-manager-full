<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;



class ProjectTaskController extends Controller
{
    use AuthorizesRequests;

    /*public function index(Project $project)
    {
        $this->authorize('view', $project);

        return TaskResource::collection(
            $project->tasks()->latest()->paginate(10)
        );
    }*/
        public function index(Request $request, Project $project)
{
    $this->authorize('view', $project);

    $query = $project->tasks()->latest();

    // 🔎 Filtro per status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔍 Ricerca testuale
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    return TaskResource::collection(
        $query->paginate(10)
    );
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

    $task = $project->tasks()->create($validated);

    return new TaskResource($task);
}

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        return new TaskResource($task);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

       $validated = $request->validate([
    'title' => 'sometimes|string|max:255',
    'description' => 'nullable|string',
    'status' => ['sometimes', Rule::in(['todo', 'doing', 'done'])],
    'due_date' => 'nullable|date'
]);

        $task->update($validated);

        return new TaskResource($task);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}