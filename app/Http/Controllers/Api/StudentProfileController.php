<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentProfileUpdateRequest;
use App\Http\Resources\StudentProfileResource;
use App\Models\StudentProfile;
use App\Services\Profiles\StudentProfileService;
use App\Services\Profiles\StudentProfileSearchService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class StudentProfileController extends Controller
{
    public function __construct(
        private readonly StudentProfileService $profiles,
        private readonly StudentProfileSearchService $searchService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $profiles = $this->searchService->searchProfiles($request);

        return response()->json([
            'success' => true,
            'data' => StudentProfileResource::collection($profiles),
            'pagination' => [
                'current_page' => $profiles->currentPage(),
                'per_page' => $profiles->perPage(),
                'total' => $profiles->total(),
                'last_page' => $profiles->lastPage(),
            ]
        ]);
    }

    public function show(StudentProfile $studentProfile): StudentProfileResource
    {
        return new StudentProfileResource($studentProfile);
    }

    public function update(StudentProfileUpdateRequest $request, StudentProfile $studentProfile): StudentProfileResource
    {
        if ($request->user()->id !== $studentProfile->user_id) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que votre propre profil.');
        }

        $data = $request->validated();
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image');
        }
        if ($request->hasFile('cv_file')) {
            $data['cv_file'] = $request->file('cv_file');
        }

        $updated = $this->profiles->update($studentProfile, $data);

        return new StudentProfileResource($updated);
    }
}
