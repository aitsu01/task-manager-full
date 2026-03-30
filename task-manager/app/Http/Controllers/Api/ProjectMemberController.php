<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectMemberController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return $project->users()
            ->select('users.id', 'users.name', 'users.email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->pivot->role
                ];
            });
    }

    public function store(Request $request, Project $project)
{
    $this->authorize('manageMembers', $project);

    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'role' => 'required|in:manager,member'
    ]);

    // Evita duplicati
    if ($project->users()->where('user_id', $validated['user_id'])->exists()) {
        return response()->json([
            'message' => 'User already member of this project'
        ], 400);
    }

    $project->users()->attach($validated['user_id'], [
        'role' => $validated['role']
    ]);

    return response()->json([
        'message' => 'Member added successfully'
    ]);
}
    public function update(Request $request, Project $project, User $user)
{
    $this->authorize('manageMembers', $project);

    $validated = $request->validate([
        'role' => 'required|in:manager,member'
    ]);

    $project->users()->updateExistingPivot($user->id, [
        'role' => $validated['role']
    ]);

    return response()->json([
        'message' => 'Role updated successfully'
    ]);
}

    public function destroy(Project $project, User $user)
{
    $this->authorize('manageMembers', $project);

    $project->users()->detach($user->id);

    return response()->json([
        'message' => 'Member removed successfully'
    ]);
}
}