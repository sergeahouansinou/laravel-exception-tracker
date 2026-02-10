<?php

namespace ExceptionTracker\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExceptionOccurred extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The exception payload data.
     */
    public array $payload;

    /**
     * Create a new message instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $exceptionClass = class_basename(
            $this->payload['exception']['class'] ?? 'Exception'
        );
        $appName = $this->payload['environment']['app_name'] ?? 'Laravel';
        $env = $this->payload['environment']['environment'] ?? 'unknown';

        return new Envelope(
            subject: "[{$appName}][{$env}] Exception: {$exceptionClass}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'exception-tracker::email',
            with: ['payload' => $this->payload],
        );
    }
}
