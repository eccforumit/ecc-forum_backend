<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfileUpdateRequest;
use App\Http\Resources\CompanyProfileResource;
use App\Models\CompanyProfile;
use App\Services\Profiles\CompanyProfileService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CompanyProfileController extends Controller
{
    public function __construct(private readonly CompanyProfileService $profiles)
    {
    }

    public function show(CompanyProfile $companyProfile): CompanyProfileResource
    {
        return new CompanyProfileResource($companyProfile);
    }

    public function update(CompanyProfileUpdateRequest $request, CompanyProfile $companyProfile): CompanyProfileResource
    {
        if ($request->user()->id !== $companyProfile->user_id) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que votre propre profil.');
        }

        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }

        $updated = $this->profiles->update($companyProfile, $data);

        return new CompanyProfileResource($updated);
    }
}
