<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $recipient;
    public string $subject;
    public string $view;
    public array $data;

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
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(string $recipient, string $subject, string $view, array $data = [])
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send($this->view, $this->data, function ($message) {
            $message->to($this->recipient)
                    ->subject($this->subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send email notification', [
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'error' => $exception->getMessage(),
        ]);
    }
}
