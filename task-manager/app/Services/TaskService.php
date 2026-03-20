<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;

class TaskService
{
    public function getPaginatedTasks(Project $project, $perPage = 10)
    {
        return $project->tasks()
            ->latest()
            ->paginate($perPage);
    }

    public function createTask(Project $project, array $data)
    {
        return $project->tasks()->create($data);
    }

    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
    }

    public function belongsToProject(Task $task, Project $project)
    {
        return $task->project_id === $project->id;
    }
}