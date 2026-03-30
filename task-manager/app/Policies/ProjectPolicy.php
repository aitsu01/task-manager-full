<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function create(User $user)
    {
        return $user->status === 'approved';
    }

    public function view(User $user, Project $project)
    {
        // Admin globale vede tutto
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function update(User $user, Project $project)
    {
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function delete(User $user, Project $project)
    {
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    public function manageMembers(User $user, Project $project)
{
    //  Admin globale 
    if ($user->role_id === 1) {
        return true;
    }

    //  Owner del progetto
    return $project->users()
        ->where('user_id', $user->id)
        ->wherePivot('role', 'owner')
        ->exists();
}
            
}