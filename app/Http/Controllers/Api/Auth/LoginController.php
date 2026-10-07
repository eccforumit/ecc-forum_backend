<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\EmailVerificationRequiredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->auth->login($request->validated());
        } catch (EmailVerificationRequiredException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'email_verification_required' => true,
                'user_email' => $exception->email,
            ], 403);
        } catch (AuthenticationException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $result['token'],
            'expires_at' => optional($result['expires_at'])->toIso8601String(),
            'user' => new UserResource($result['user']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->auth->currentUser($request->user());

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
        ]);
    }
}
