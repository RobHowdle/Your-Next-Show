<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Your Next Show!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $logoPath = public_path('images/system/yns_logo.png');

        return new Content(
            view: 'emails.welcome',
            with: [
                'logoPath' => $logoPath,
                'logoExists' => file_exists($logoPath)
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
        $logoPath = public_path('images/system/yns_logo.png');

        if (file_exists($logoPath)) {
            return [
                Attachment::fromPath($logoPath)
                    ->as('logo.png')
                    ->withMime('image/png')
            ];
        }

        return [];
    }
}