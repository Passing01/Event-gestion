<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

use App\Notifications\Channels\EmailJSChannel;

class VerifyEmailStyled extends VerifyEmail
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
                    ->subject('[' . config('app.name', 'Event Q&A') . '] Vérifiez votre adresse e-mail')
                    ->view('emails.verify', ['url' => $verificationUrl]);
    }
}
