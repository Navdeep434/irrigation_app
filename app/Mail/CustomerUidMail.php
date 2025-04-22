<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CustomerUidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $uid;
    public $name;
    public $appName;
    public $supportEmail;
    public $dashboardUrl;

    public function __construct($name, $uid)
    {
        $this->name = $name;
        $this->uid = $uid;
        $this->appName = env('APP_NAME');
        $this->supportEmail = env('SUPPORT_EMAIL');
        $this->dashboardUrl = env('DASHBOARD_URL', '#');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Unique Customer ID');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-uid',
            with: [
                'name' => $this->name,
                'uid' => $this->uid,
                'appName' => $this->appName,
                'supportEmail' => $this->supportEmail,
                'dashboardUrl' => $this->dashboardUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
