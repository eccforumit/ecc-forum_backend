<?php

namespace App\Services\Auth;

use App\Exceptions\EmailVerificationRequiredException;
use App\Models\User;
use App\Services\Notification\EmailNotificationService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function __construct(private readonly EmailNotificationService $emails)
    {
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Email ou mot de passe incorrect.');
        }

        if (!$user->is_active) {
            throw new HttpException(403, 'Ce compte est désactivé.');
        }

        if (!$user->is_email_verified) {
            throw new EmailVerificationRequiredException($user->email);
        }

        $remember = (bool)($credentials['remember_me'] ?? false);

        $expiresAt = $remember ? now()->addDays(30) : null;

        $token = $user->createToken('api-token', ['*'], $expiresAt);

        $user->forceFill([
            'last_login' => now(),
        ])->save();

        $user->load(['studentProfile', 'companyProfile']);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => $expiresAt,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function currentUser(User $user): User
    {
        return $user->load(['studentProfile', 'companyProfile']);
    }

    public function generateEmailVerification(User $user): string
    {
        $token = Str::random(64);
        $user->email_verification_token = hash('sha256', $token);
        $user->save();

        $uid = base64_encode((string) $user->getKey());
        $frontend = config('services.frontend.url');
        $verificationUrl = rtrim($frontend, '/')."/auth/verify-email/{$uid}/{$token}";

        $this->emails->sendVerificationEmail($user, $verificationUrl, $frontend);

        return $verificationUrl;
    }

    public function verifyEmail(string $uid, string $token): User
    {
        $user = $this->resolveUserFromUid($uid);

        if (!$user->email_verification_token) {
            throw new HttpException(400, 'Lien de vérification invalide ou expiré.');
        }

        if (!hash_equals($user->email_verification_token, hash('sha256', $token))) {
            throw new HttpException(400, 'Token de vérification invalide ou expiré.');
        }

        $user->forceFill([
            'is_email_verified' => true,
            'email_verification_token' => null,
        ])->save();

        return $user->fresh(['studentProfile', 'companyProfile']);
    }

    public function resendVerification(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new HttpException(404, 'Aucun compte trouvé avec cette adresse email.');
        }

        if ($user->is_email_verified) {
            throw new HttpException(409, 'Cette adresse email est déjà vérifiée.');
        }

        $this->generateEmailVerification($user);
    }

    protected function resolveUserFromUid(string $encoded): User
    {
        $decoded = base64_decode($encoded, true);

        if ($decoded === false) {
            throw new HttpException(400, 'Identifiant de vérification invalide.');
        }

        $user = User::find($decoded);

        if (!$user) {
            throw new HttpException(404, 'Utilisateur introuvable.');
        }

        return $user;
    }
}
