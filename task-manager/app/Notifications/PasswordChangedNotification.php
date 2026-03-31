<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('La tua password è stata modificata')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('La tua password è stata modificata con successo.')
            ->line('Se non sei stato tu, contatta immediatamente il supporto.')
            ->salutation('Task Manager Security');
    }
}