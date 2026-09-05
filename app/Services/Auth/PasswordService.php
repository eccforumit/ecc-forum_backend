<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Notification\EmailNotificationService;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordService
{
    public function __construct(private readonly EmailNotificationService $emails)
    {
    }

    public function sendResetLink(string $email): void
    {
        $user = User::where('email', $email)->where('is_active', true)->first();

        if (!$user) {
            return; // do not leak existence
        }

        $token = Str::random(64);
        $user->forceFill([
            'password_reset_token' => hash('sha256', $token),
            'password_reset_expires' => now()->addDay(),
        ])->save();

        $uid = base64_encode((string) $user->getKey());
        $frontend = config('services.frontend.url');
        $resetUrl = rtrim($frontend, '/')."/auth/reset-password/{$uid}/{$token}";

        $this->emails->sendPasswordResetEmail($user, $resetUrl, $frontend);
    }

    public function resetPassword(string $uid, string $token, string $newPassword): void
    {
        $decoded = base64_decode($uid, true);
        if ($decoded === false) {
            throw new HttpException(400, 'Lien de réinitialisation invalide.');
        }

        $user = User::find($decoded);
        if (!$user || !$user->password_reset_token) {
            throw new HttpException(400, 'Lien de réinitialisation invalide.');
        }

        if (!hash_equals($user->password_reset_token, hash('sha256', $token))) {
            throw new HttpException(400, 'Token de réinitialisation invalide ou expiré.');
        }

        if ($user->password_reset_expires && now()->greaterThan($user->password_reset_expires)) {
            throw new HttpException(400, 'Token de réinitialisation expiré.');
        }

        $user->forceFill([
            'password' => $newPassword,
            'password_reset_token' => null,
            'password_reset_expires' => null,
        ])->save();

        $user->tokens()->delete();
    }
}
