<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SuperadminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;
    public $ownerName;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $otp, $ownerName = null)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->ownerName = $ownerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'OTP for New Superadmin Approval'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.superadmin-otp',
            with: [
                'user' => $this->user,
                'otp' => $this->otp,
                'ownerName' => $this->ownerName,
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
