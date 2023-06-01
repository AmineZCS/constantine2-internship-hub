<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $user_info;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $user_info)
    {
        $this->user = $user;
        $this->user_info = $user_info;
    
    
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome Email',
            from: new Address('noreply@internshipaxis.me', 'Internship Axis'),
            
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'WelcomeEmail',
            with: [
                'user' => $this->user,
                'user_info' => $this->user_info,
            ],
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
