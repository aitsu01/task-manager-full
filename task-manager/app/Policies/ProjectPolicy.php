<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;


class ProjectPolicy
{
    /**
     * Tutti gli utenti autenticati possono creare progetti
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Può vedere il progetto se è membro
     */
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

    /**
     * Può modificare se è owner o manager
     */
    public function update(User $user, Project $project)
    {
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }

    /**
     * Solo owner può eliminare
     */
    public function delete(User $user, Project $project)
    {
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }

    /**
     * Solo owner può gestire membri
     */
    public function manageMembers(User $user, Project $project)
    {
        if ($user->role && $user->role->name === 'admin') {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }
}