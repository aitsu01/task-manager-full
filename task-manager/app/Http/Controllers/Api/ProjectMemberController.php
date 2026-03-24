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

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:manager,member'
        ]);

        // Evita duplicati
        if ($project->users()->where('user_id', $data['user_id'])->exists()) {
            return response()->json([
                'message' => 'User already member'
            ], 400);
        }

        $project->users()->attach($data['user_id'], [
            'role' => $data['role']
        ]);

        return response()->json([
            'message' => 'Member added successfully'
        ]);
    }

    public function update(Request $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        $data = $request->validate([
            'role' => 'required|in:manager,member'
        ]);

        $project->users()->updateExistingPivot($user->id, [
            'role' => $data['role']
        ]);

        return response()->json([
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        // Impedisce di rimuovere l'owner
        $member = $project->users()
            ->where('user_id', $user->id)
            ->first();

        if ($member && $member->pivot->role === 'owner') {
            return response()->json([
                'message' => 'Cannot remove owner'
            ], 400);
        }

        $project->users()->detach($user->id);

        return response()->json([
            'message' => 'Member removed successfully'
        ]);
    }
}