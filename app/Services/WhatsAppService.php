<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppService
{
    protected string $url;
    protected string $token;

    public function __construct()
    {
        $url = env('WAG_URL');
        $this->url = rtrim(!empty($url) ? $url : 'https://waghub.mekayastudio.com', '/');

        $token = env('WAG_TOKEN');
        $this->token = !empty($token) ? $token : 'wgh_3MrWO6Nm9YERLfzjxYkgdqe5XxUkP6Dd3gVADWCRO4FraqnqtG6Sp3mDgo9ZEj7v';
    }

    /**
     * Normalize Indonesian phone number (08xx -> 628xx)
     */
    public function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }
        return $clean;
    }

    /**
     * Send message via WAGHub v1 messages API
     */
    public function sendMessage(string $phone, string $message, string $purpose = 'notification'): array
    {
        $target = $this->normalizePhone($phone);
        $idempotencyKey = 'scm-' . (string) Str::uuid();
        $endpoint = $this->url . '/api/v1/messages';

        $payload = [
            'idempotency_key' => $idempotencyKey,
            'recipient' => [
                'type' => 'phone',
                'value' => $target
            ],
            'message' => [
                'type' => 'text',
                'text' => $message
            ],
            'purpose' => in_array($purpose, ['otp', 'transactional', 'notification']) ? $purpose : 'notification',
            'mode' => 'sync',
            'expires_at' => gmdate('Y-m-d\TH:i:s\Z', time() + 86400)
        ];

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withToken($this->token)
                ->withHeaders([
                    'Idempotency-Key' => $idempotencyKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("WAGHub message delivered to {$target}: " . $response->body());
                return [
                    'success' => true,
                    'target' => $target,
                    'data' => $response->json()
                ];
            } else {
                Log::warning("WAGHub error {$response->status()}: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("WAGHub exception dispatching to {$target}: " . $e->getMessage());
        }

        return [
            'success' => false,
            'target' => $target
        ];
    }

    /**
     * Send OTP message via WhatsApp
     */
    public function sendOtp(string $phone, string $otp, string $name = 'Pengguna SCM'): array
    {
        $message = "Kode OTP SCM TaxVault Anda: *{$otp}*\n\n" .
                   "Kode ini berlaku selama 5 menit. Jangan bagikan kode ini kepada siapa pun.\n\n" .
                   "Reza - IT Development";

        return $this->sendMessage($phone, $message, 'otp');
    }

    /**
     * Send Registration Received notification
     */
    public function sendRegistrationNotice(string $phone, string $name, string $role): array
    {
        $message = "Halo *{$name}*,\n\n" .
                   "Pendaftaran akun Anda untuk role *{$role}* sudah kami terima dan saat ini sedang menunggu persetujuan (ACC) dari Admin.\n\n" .
                   "Notifikasi akan dikirim kembali setelah akun disetujui.\n\n" .
                   "Reza - IT Development";

        return $this->sendMessage($phone, $message, 'notification');
    }

    /**
     * Send Account Approved (ACC) notification
     */
    public function sendAccountApprovedNotice(string $phone, string $name, string $role): array
    {
        $message = "Halo *{$name}*,\n\n" .
                   "Akun Anda untuk role *{$role}* sudah disetujui (ACC). Anda sekarang sudah bisa masuk ke sistem SCM TaxVault.\n\n" .
                   "Reza - IT Development";

        return $this->sendMessage($phone, $message, 'notification');
    }
}
