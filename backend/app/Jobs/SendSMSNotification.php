<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phoneNumber;
    public string $message;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phoneNumber, string $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // In production, integrate with Tunisian SMS provider (e.g., Ooredoo, Orange, Tunisie Telecom)
        // For now, just log the SMS
        \Log::info('SMS Notification', [
            'phone' => $this->phoneNumber,
            'message' => $this->message,
            'timestamp' => now()->toIso8601String(),
        ]);

        // Example integration with SMS API:
        /*
        Http::post('https://sms-provider.tn/api/send', [
            'api_key' => config('services.sms.api_key'),
            'phone' => $this->phoneNumber,
            'message' => $this->message,
            'sender' => 'SehaDigital',
        ]);
        */
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send SMS notification', [
            'phone' => $this->phoneNumber,
            'error' => $exception->getMessage(),
        ]);
    }
}
