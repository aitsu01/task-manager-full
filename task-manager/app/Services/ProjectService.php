<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function getPaginatedProjects($user, $perPage = 10)
    {
        $query = Project::query();

        // Se NON admin → solo i suoi progetti
        if (!$user->role || $user->role->name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query
            ->withCount('tasks')
            ->latest()
            ->paginate($perPage);
    }

    public function createProject($user, array $data)
    {
        return $user->projects()->create($data);
    }

    public function updateProject(Project $project, array $data)
    {
        $project->update($data);
        return $project;
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
    }
}