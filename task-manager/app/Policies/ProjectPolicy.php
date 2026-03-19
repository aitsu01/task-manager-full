<?php

namespace App\Policies;


use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;


class ProjectPolicy
{
    public function view(User $user, Project $project)
{
    return $user->isAdmin() || $project->user_id === $user->id;
}

public function update(User $user, Project $project)
{
    return $user->isAdmin() || $project->user_id === $user->id;
}

public function delete(User $user, Project $project)
{
    return $user->isAdmin() || $project->user_id === $user->id;
}
    
}