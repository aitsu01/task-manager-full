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
        'user_id' => 'nullable|exists:users,id',
        'email' => 'nullable|email',
        'role' => 'required|in:manager,member'
    ]);

    //  trova utente
    if (!empty($data['user_id'])) {
        $user = User::find($data['user_id']);
    } elseif (!empty($data['email'])) {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    } else {
        return response()->json([
            'message' => 'User ID or email required'
        ], 422);
    }

    //  evita duplicati
    if ($project->users()->where('user_id', $user->id)->exists()) {
        return response()->json([
            'message' => 'User already member'
        ], 400);
    }

    // ✅ attach
    $project->users()->attach($user->id, [
        'role' => $data['role']
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