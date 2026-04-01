<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $oldStatus;

    public function __construct($task, $oldStatus)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
    }

    public function via($notifiable)
    {
        return ['database']; // 🔥 SOLO DB
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Task '{$this->task->title}' cambiata da {$this->oldStatus} a {$this->task->status}",
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id
        ];
    }
}