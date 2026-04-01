<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PanelistInvitation extends Notification
{
    use Queueable;

    protected $event;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, $password)
    {
        $this->event = $event;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Invitation en tant que Panéliste - ' . $this->event->name)
                    ->greeting('Bonjour ' . $notifiable->name . ' !')
                    ->line('Vous avez été invité en tant que panéliste pour l\'événement : ' . $this->event->name)
                    ->line('Voici vos informations de connexion :')
                    ->line('Email : ' . $notifiable->email)
                    ->line('Mot de passe : ' . $this->password)
                    ->line('Code de l\'événement : ' . $this->event->code)
                    ->action('Accéder à l\'événement', url('/join'))
                    ->line('Veuillez changer votre mot de passe après votre première connexion.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
        ];
    }
}
