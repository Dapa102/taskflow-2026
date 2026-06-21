<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $phone = $notifiable->phone;
        if (!$phone) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        if (!$message) {
            return;
        }

        $token = config("fonnte.token");
        if (!$token) {
            Log::warning("Fonnte token not configured");
            return;
        }

        $response = Http::withHeaders([
            "Authorization" => $token,
        ])->post("https://api.fonnte.com/send", [
            "target" => $phone,
            "message" => $message,
            "countryCode" => "62",
        ]);

        if ($response->failed()) {
            Log::error("Fonnte send failed", [
                "phone" => $phone,
                "response" => $response->body(),
            ]);
        }
    }
}
