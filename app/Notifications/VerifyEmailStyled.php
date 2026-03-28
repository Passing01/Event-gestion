<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

use App\Notifications\Channels\EmailJSChannel;

class VerifyEmailStyled extends VerifyEmail
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [EmailJSChannel::class];
    }

    /**
     * Get the EmailJS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toEmailJS($notifiable)
    {
        return [
            'user_name' => $notifiable->name,
            'user_email' => $notifiable->email,
            'verification_url' => $this->verificationUrl($notifiable),
        ];
    }
}
