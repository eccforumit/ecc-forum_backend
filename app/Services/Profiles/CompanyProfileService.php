<?php

namespace App\Services\Profiles;

use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\Media\CloudinaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CompanyProfileService
{
    public function __construct(private readonly CloudinaryService $cloudinary)
    {
    }

    public function register(array $input): User
    {
        return DB::transaction(function () use ($input) {
            /** @var UploadedFile|null $logo */
            $logo = $input['logo'] ?? null;

            $user = User::create([
                'email' => $input['email'],
                'password' => $input['password'],
                'user_type' => 'company',
                'first_name' => $input['contact_first_name'],
                'last_name' => $input['contact_last_name'],
                'phone_number' => $input['contact_phone'],
            ]);

            $profileData = [
                'user_id' => $user->id,
                'company_name' => $input['company_name'],
                'industry' => $input['industry'],
                'company_size' => $input['company_size'] ?? null,
                'company_description' => $input['company_description'] ?? null,
                'website' => $input['website'] ?? null,
                'address' => $input['address'] ?? null,
                'contact_first_name' => $input['contact_first_name'],
                'contact_last_name' => $input['contact_last_name'],
                'contact_phone' => $input['contact_phone'],
            ];

            if ($logo instanceof UploadedFile) {
                $upload = $this->cloudinary->upload($logo, 'forum/companies', 'image');
                $profileData['logo_url'] = $upload['url'];
                $profileData['logo_public_id'] = $upload['public_id'];
            }

            CompanyProfile::create($profileData);

            return $user->load('companyProfile');
        });
    }

    public function update(CompanyProfile $profile, array $input): CompanyProfile
    {
        return DB::transaction(function () use ($profile, $input) {
            /** @var UploadedFile|null $logo */
            $logo = $input['logo'] ?? null;

            $profile->fill([
                'company_name' => $input['company_name'] ?? $profile->company_name,
                'industry' => $input['industry'] ?? $profile->industry,
                'company_size' => $input['company_size'] ?? $profile->company_size,
                'company_description' => $input['company_description'] ?? $profile->company_description,
                'website' => $input['website'] ?? $profile->website,
                'address' => $input['address'] ?? $profile->address,
                'contact_first_name' => $input['contact_first_name'] ?? $profile->contact_first_name,
                'contact_last_name' => $input['contact_last_name'] ?? $profile->contact_last_name,
                'contact_phone' => $input['contact_phone'] ?? $profile->contact_phone,
            ]);

            if ($logo instanceof UploadedFile) {
                $this->cloudinary->delete($profile->logo_public_id);
                $upload = $this->cloudinary->upload($logo, 'forum/companies', 'image');
                $profile->logo_url = $upload['url'];
                $profile->logo_public_id = $upload['public_id'];
            }

            $profile->save();

            $profile->user->update([
                'first_name' => $profile->contact_first_name,
                'last_name' => $profile->contact_last_name,
                'phone_number' => $profile->contact_phone,
            ]);

            return $profile;
        });
    }
}
