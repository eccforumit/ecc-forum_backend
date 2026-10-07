<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRegistrationRequest;
use App\Http\Requests\StudentRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Services\Profiles\CompanyProfileService;
use App\Services\Profiles\StudentProfileService;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $students,
        private readonly CompanyProfileService $companies,
        private readonly AuthService $auth,
    ) {
    }

    public function student(StudentRegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image');
        }
        if ($request->hasFile('cv_file')) {
            $data['cv_file'] = $request->file('cv_file');
        }

        $user = $this->students->register($data);
        $this->auth->generateEmailVerification($user);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Un email de vérification a été envoyé.',
            'user' => new UserResource($user),
            'email_sent' => true,
            'requires_verification' => true,
        ], 201);
    }

    public function company(CompanyRegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }

        $user = $this->companies->register($data);
        $this->auth->generateEmailVerification($user);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Un email de vérification a été envoyé.',
            'user' => new UserResource($user),
            'email_sent' => true,
            'requires_verification' => true,
        ], 201);
    }
}
