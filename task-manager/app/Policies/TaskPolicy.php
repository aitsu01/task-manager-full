<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * View any tasks (non necessario nel tuo caso)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * View singola task
     */
    public function view(User $user, Task $task): bool
    {
        // Admin globale
        if ($user->isAdmin()) return true;

        // membro del progetto
        return $task->project->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Creare task (membri del progetto)
     */
    public function create(User $user): bool
    {
        // qui non hai il project → gestisci nel controller
        return true;
    }

    public function update(User $user, Task $task): bool
{
    if ($user->isAdmin()) return true;

    // SOLO OWNER può modificare task
    return $task->project->users()
        ->where('user_id', $user->id)
        ->wherePivot('role', 'owner')
        ->exists();
}

public function delete(User $user, Task $task): bool
{
    if ($user->isAdmin()) return true;

    return $task->project->users()
        ->where('user_id', $user->id)
        ->wherePivot('role', 'owner')
        ->exists();
}
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
public function updateStatus(User $user, Task $task): bool
{
    if ($user->isAdmin()) return true;

    return $task->project->users()
        ->where('user_id', $user->id)
        ->wherePivot('role', 'owner')
        ->exists();
}







}