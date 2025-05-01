<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Throwable;

class MailtrapService
{
    protected string $apiToken;
    protected string $endpoint;
    protected string $defaultFromEmail;
    protected string $defaultFromName;
    protected string $inboxId;

    public function __construct()
    {
        $this->apiToken = env('MAILTRAP_API_TOKEN');
        $this->endpoint = 'https://sandbox.api.mailtrap.io/api/send';
        $this->inboxId = env('MAILTRAP_INBOX_ID');
        $this->defaultFromEmail = env('MAIL_FROM_ADDRESS', 'noreply@example.com');
        $this->defaultFromName = env('MAIL_FROM_NAME', 'Example App');

        if (!$this->apiToken || !$this->endpoint) {
            throw new \InvalidArgumentException('Mailtrap API token or endpoint is not configured.');
        }
    }

    /**
     * Sends a verification email using the Mailtrap HTTP API.
     *
     * @param string $recipientEmail Recipient's email address.
     * @param string $recipientName Recipient's name (optional).
     * @param string $verificationUrl Signed verification URL.
     * @return bool True if the sending was successful (according to Mailtrap), false otherwise.
     */
    public function sendVerificationEmail(string $recipientEmail, string $recipientName, string $verificationUrl): bool
    {
        $subject = 'Activate your account';

        // Asegúrate de crear la vista: resources/views/emails/verify-account-mailtrap.blade.php
        $htmlBody = View::make('emails.verify-account-mailtrap', [
            'recipientName' => $recipientName,
            'verificationUrl' => $verificationUrl,
            'appName' => $this->defaultFromName,
        ])->render();


        $payload = [
            'from' => [
                'email' => $this->defaultFromEmail,
                'name' => $this->defaultFromName,
            ],
            'to' => [
                [
                    'email' => $recipientEmail,
                    'name' => $recipientName,
                ]
            ],
            'subject' => $subject,
            'html' => $htmlBody,
        ];

        try {
            $response = Http::asJson()
                ->withHeaders([
                    'Api-Token' => $this->apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($this->endpoint . '/' . $this->inboxId, $payload);


            Log::info("response");
            Log::info($response);

            if ($response->successful()) {
                Log::info('Mailtrap verification email sent successfully.', ['email' => $recipientEmail, 'response' => $response->json()]);
                return $response->json('success', false);
            } else {
                Log::error('Failed to send Mailtrap verification email.', [
                    'email' => $recipientEmail,
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'payload_sent' => $payload
                ]);
                return false;
            }
        } catch (Throwable $e) {
            Log::error('Exception while sending Mailtrap verification email.', [
                'email' => $recipientEmail,
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }
}