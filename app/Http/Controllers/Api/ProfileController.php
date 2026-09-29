<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentProfileUpdateRequest;
use App\Http\Resources\StudentProfileResource;
use App\Http\Resources\CompanyProfileResource;
use App\Services\Profiles\StudentProfileService;
use App\Services\Profiles\CompanyProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $studentProfiles,
        private readonly CompanyProfileService $companyProfiles
    ) {
    }

    private function getOrCreateStudentProfile($user)
    {
        return $user->studentProfile ?: $user->studentProfile()->create([
            'first_name' => '',
            'last_name' => '',
        ]);
    }

    private function getOrCreateCompanyProfile($user)
    {
        return $user->companyProfile ?: $user->companyProfile()->create([
            'company_name' => '',
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $this->getOrCreateStudentProfile($user);
            return response()->json([
                'success' => true,
                'data' => new StudentProfileResource($profile->load('cvDocuments'))
            ]);
        }

        if ($user->isCompany()) {
            $profile = $this->getOrCreateCompanyProfile($user);
            return response()->json([
                'success' => true,
                'data' => new CompanyProfileResource($profile)
            ]);
        }

        throw new NotFoundHttpException('Type de profil non supporté');
    }

    public function update(StudentProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            $profile = $this->getOrCreateStudentProfile($user);

            $data = $request->validated();
            
            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image');
            }
            if ($request->hasFile('cv_file')) {
                $data['cv_file'] = $request->file('cv_file');
            }

            $updated = $this->studentProfiles->update($profile, $data);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => new StudentProfileResource($updated)
            ]);
        }

        throw new NotFoundHttpException('Type de profil non supporté pour cette action');
    }
}
