<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminActionEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly string $intro,
        private readonly array $details = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.admin-action', [
                'subject' => $this->subject,
                'intro' => $this->intro,
                'details' => $this->details,
            ])
            ->text('emails.admin-action-text', [
                'subject' => $this->subject,
                'intro' => $this->intro,
                'details' => $this->details,
            ]);
    }
}
