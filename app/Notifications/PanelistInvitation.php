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
                    ->subject('[' . config('app.name', 'Event Q&A') . '] Vos accès Panéliste - ' . $this->event->name)
                    ->view('emails.panelist-access', [
                        'userName'  => $notifiable->name,
                        'userEmail' => $notifiable->email,
                        'password'  => $this->password,
                        'eventName' => $this->event->name,
                        'appName'   => config('app.name', 'Event Q&A'),
                        'loginUrl'  => url('/signin'),
                    ]);
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
