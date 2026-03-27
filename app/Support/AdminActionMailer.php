<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AdminActionEmailNotification;
use Illuminate\Support\Facades\Notification;

class AdminActionMailer
{
    public static function send(string $subject, string $intro, array $details = []): void
    {
        $adminEmail = env('ADMIN_EMAIL') ?: User::query()->orderBy('id')->value('email');

        if (! $adminEmail) {
            return;
        }

        Notification::route('mail', $adminEmail)
            ->notify(new AdminActionEmailNotification($subject, $intro, $details));
    }
}
