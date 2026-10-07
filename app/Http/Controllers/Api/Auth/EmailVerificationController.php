<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EmailVerificationController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function verify(string $uid, string $token): JsonResponse
    {
        $user = $this->auth->verifyEmail($uid, $token);

        return response()->json([
            'success' => true,
            'message' => 'Votre email a été vérifié avec succès !',
            'user' => new UserResource($user),
        ]);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $this->auth->resendVerification($request->input('email'));
        } catch (HttpException $exception) {
            $status = $exception->getStatusCode();
            $payload = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];

            return response()->json($payload, $status);
        }

        return response()->json([
            'success' => true,
            'message' => 'Un email de vérification a été envoyé à votre adresse.',
        ]);
    }
}
