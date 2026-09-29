<?php

namespace App\Services\Notification;

use App\Mail\PasswordResetMail;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    public function sendVerificationEmail(User $user, string $verificationUrl, string $frontendUrl): void
    {
        Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl, $frontendUrl));
    }

    public function sendPasswordResetEmail(User $user, string $resetUrl, string $frontendUrl): void
    {
        Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl, $frontendUrl));
    }
}
