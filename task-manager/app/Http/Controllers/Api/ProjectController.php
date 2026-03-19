<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\ProjectResource;



class ProjectController extends Controller
{
    use AuthorizesRequests;

 public function index(Request $request)
{
    $query = Project::query();

    $user = $request->user();

    if (! $user->role || $user->role->name !== 'admin') {
        $query->where('user_id', $user->id);
    }

    return ProjectResource::collection(
        $query->withCount('tasks')
              ->latest()
              ->paginate(10)
    );
}

   public function store(StoreProjectRequest $request)
{
    $project = $request->user()->projects()->create(
        $request->validated()
    );

    return new ProjectResource($project);
}

    
    public function show(Project $project)
{
    $this->authorize('view', $project);

    return new ProjectResource($project);
}

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        /*return response()->json($project);*/
        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }

}