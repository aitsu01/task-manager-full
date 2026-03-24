<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    public function getPaginatedProjects($user, $perPage = 10)
    {
        $query = Project::query();

        // Admin vede tutto
        if (!$user->role || $user->role->name !== 'admin') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query
            ->withCount('tasks')
            ->latest()
            ->paginate($perPage);
    }

    public function createProject($user, array $data)
    {
        $project = Project::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Creator diventa owner
        $project->users()->attach($user->id, [
            'role' => 'owner'
        ]);

        return $project;
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