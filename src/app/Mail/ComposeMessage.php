<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComposeMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $body;
    public string $senderName;

    public function __construct(string $subject, string $body, string $senderName)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->senderName = $senderName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.compose',
            with: [
                'subject' => $this->subject,
                'body' => $this->body,
                'senderName' => $this->senderName,
            ],
        );
    }
}
