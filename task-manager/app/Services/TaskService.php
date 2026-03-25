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



    public function deleteTask(Task $task)
    {
        $task->delete();
    }

    public function belongsToProject(Task $task, Project $project)
    {
        return $task->project_id === $project->id;
    }

    public function updateTask($task, array $data)
{
    $originalStatus = $task->status;

    $task->fill($data);

    // Se cambia stato
    if (isset($data['status'])) {

        if ($data['status'] === 'done' && $originalStatus !== 'done') {
            $task->completed_at = now();
        }

        if ($originalStatus === 'done' && $data['status'] !== 'done') {
            $task->completed_at = null;
        }
    }

    // Validazione assegnazione membro
    if (isset($data['assigned_user_id'])) {

        $project = $task->project;

        $isMember = $project->users()
            ->where('user_id', $data['assigned_user_id'])
            ->exists();

        if (!$isMember) {
            abort(403, 'User is not a member of this project');
        }

        $task->assigned_user_id = $data['assigned_user_id'];
    }

    $task->save();

    return $task;
}



}