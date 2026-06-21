<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected string $token;
    protected string $endpoint;

    public function __construct()
    {
        $this->token = config('fonnte.token');
        $this->endpoint = config('fonnte.endpoint');
    }

    public function send(string $phone, string $message): bool
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post($this->endpoint, [
            'target' => $phone,
            'message' => $message,
        ]);

        return $response->successful();
    }

    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
