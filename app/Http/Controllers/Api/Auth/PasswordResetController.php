<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetConfirmRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Services\Auth\PasswordService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordResetController extends Controller
{
    public function __construct(private readonly PasswordService $passwords)
    {
    }

    public function request(PasswordResetRequest $request): JsonResponse
    {
        $this->passwords->sendResetLink($request->validated()['email']);

        return response()->json([
            'success' => true,
            'message' => 'Un email de réinitialisation a été envoyé à votre adresse.',
        ]);
    }

    public function reset(PasswordResetConfirmRequest $request): JsonResponse
    {
        try {
            $this->passwords->resetPassword(
                $request->validated()['uid'],
                $request->validated()['token'],
                $request->validated()['new_password']
            );
        } catch (HttpException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.',
        ]);
    }
}
