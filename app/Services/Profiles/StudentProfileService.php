<?php

namespace App\Services\Profiles;

use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Media\CloudinaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class StudentProfileService
{
    public function __construct(private readonly CloudinaryService $cloudinary)
    {
    }

    public function register(array $input): User
    {
        return DB::transaction(function () use ($input) {
            /** @var UploadedFile|null $profileImage */
            $profileImage = $input['profile_image'] ?? null;
            /** @var UploadedFile|null $cvFile */
            $cvFile = $input['cv_file'] ?? null;

            $user = User::create([
                'email' => $input['email'],
                'password' => $input['password'],
                'user_type' => 'student',
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'phone_number' => $input['phone_number'] ?? null,
            ]);

            $profileData = [
                'user_id' => $user->id,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'phone_number' => $input['phone_number'] ?? null,
                'school' => $input['school'],
                'school_name' => $input['school_name'] ?? null,
                'major' => $input['major'],
                'school_year' => $input['school_year'],
                'linkedin_url' => $input['linkedin_url'] ?? null,
                'github_url' => $input['github_url'] ?? null,
                'portfolio_url' => $input['portfolio_url'] ?? null,
            ];

            if ($profileImage instanceof UploadedFile) {
                try {
                    $upload = $this->cloudinary->upload($profileImage, 'forum/students', 'image');
                    $profileData['profile_image_url'] = $upload['url'];
                    $profileData['profile_image_public_id'] = $upload['public_id'];
                } catch (\Exception $e) {
                    \Log::warning('Profile image upload failed during registration: ' . $e->getMessage());
                    // Continue with registration without profile image
                }
            }

            if ($cvFile instanceof UploadedFile) {
                try {
                    $upload = $this->cloudinary->upload($cvFile, 'forum/cvs', 'raw');
                    $profileData['cv_file_url'] = $upload['url'];
                    $profileData['cv_file_public_id'] = $upload['public_id'];
                } catch (\Exception $e) {
                    \Log::warning('CV file upload failed during registration: ' . $e->getMessage());
                    // Continue with registration without CV file
                }
            }

            StudentProfile::create($profileData);

            return $user->load('studentProfile');
        });
    }

    public function update(StudentProfile $profile, array $input): StudentProfile
    {
        return DB::transaction(function () use ($profile, $input) {
            /** @var UploadedFile|null $profileImage */
            $profileImage = $input['profile_image'] ?? null;
            /** @var UploadedFile|null $cvFile */
            $cvFile = $input['cv_file'] ?? null;

            $profile->fill([
                'first_name' => $input['first_name'] ?? $profile->first_name,
                'last_name' => $input['last_name'] ?? $profile->last_name,
                'phone_number' => $input['phone_number'] ?? $profile->phone_number,
                'school' => $input['school'] ?? $profile->school,
                'school_name' => $input['school_name'] ?? $profile->school_name,
                'major' => $input['major'] ?? $profile->major,
                'school_year' => $input['school_year'] ?? $profile->school_year,
                'linkedin_url' => $input['linkedin_url'] ?? $profile->linkedin_url,
                'github_url' => $input['github_url'] ?? $profile->github_url,
                'portfolio_url' => $input['portfolio_url'] ?? $profile->portfolio_url,
            ]);

            if ($profileImage instanceof UploadedFile) {
                $this->cloudinary->delete($profile->profile_image_public_id);
                $upload = $this->cloudinary->upload($profileImage, 'forum/students', 'image');
                $profile->profile_image_url = $upload['url'];
                $profile->profile_image_public_id = $upload['public_id'];
            }

            if ($cvFile instanceof UploadedFile) {
                $this->cloudinary->delete($profile->cv_file_public_id);
                $upload = $this->cloudinary->upload($cvFile, 'forum/cvs', 'auto');
                $profile->cv_file_url = $upload['url'];
                $profile->cv_file_public_id = $upload['public_id'];
            }

            $profile->save();

            $profile->load('user');
            $profile->user->update([
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'phone_number' => $profile->phone_number,
            ]);

            return $profile;
        });
    }
}
