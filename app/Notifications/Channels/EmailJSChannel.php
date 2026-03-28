<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailJSChannel
{
    /**
     * Envoyer la notification via EmailJS API.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Illuminate\Http\Client\Response|null
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toEmailJS')) {
            return null;
        }

        $data = $notification->toEmailJS($notifiable);

        $response = Http::post('https://api.emailjs.com/api/v1.0/email/send', [
            'service_id' => env('VITE_EMAILJS_SERVICE_ID'),
            'template_id' => env('VITE_EMAILJS_TEMPLATE_ID'),
            'user_id' => env('VITE_EMAILJS_PUBLIC_KEY'),
            'template_params' => $data,
        ]);

        if (!$response->successful()) {
            Log::error('Erreur EmailJS : ' . $response->body());
        }

        return $response;
    }
}
