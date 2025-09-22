<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subject,
        public string $message,
        public array $data = [],
        public string $type = 'info' // info, success, warning, error
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.system-notification',
            with: [
                'notificationMessage' => $this->message,
                'notificationData' => $this->data,
                'notificationType' => $this->type,
                'appName' => config('app.name'),
                'appUrl' => config('app.url'),
                'timestamp' => now()->format('Y-m-d H:i:s T'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
