<?php

namespace App\Services\Opportunities;

use App\Models\CompanyProfile;
use App\Models\Opportunity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpportunityService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Opportunity::query()->with('companyProfile.user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('skills', 'like', $search);
            });
        }

        $perPage = (int)($filters['per_page'] ?? 15);

        return $query->orderByDesc('published_at')->paginate($perPage);
    }

    public function create(CompanyProfile $companyProfile, array $input): Opportunity
    {
        return DB::transaction(function () use ($companyProfile, $input) {
            $slug = Str::slug($input['title'].'-'.$companyProfile->id.'-'.Str::random(6));

            $opportunity = Opportunity::create([
                'company_profile_id' => $companyProfile->id,
                'title' => $input['title'],
                'slug' => $slug,
                'location' => $input['location'] ?? null,
                'employment_type' => $input['employment_type'] ?? null,
                'is_remote' => (bool)($input['is_remote'] ?? false),
                'salary_range' => $input['salary_range'] ?? null,
                'skills' => $input['skills'] ?? [],
                'description' => $input['description'],
                'requirements' => $input['requirements'] ?? null,
                'benefits' => $input['benefits'] ?? null,
                'status' => $input['status'] ?? 'draft',
                'published_at' => $input['status'] === 'published' ? now() : null,
            ]);

            return $opportunity;
        });
    }

    public function update(Opportunity $opportunity, array $input): Opportunity
    {
        return DB::transaction(function () use ($opportunity, $input) {
            $opportunity->fill([
                'title' => $input['title'] ?? $opportunity->title,
                'location' => $input['location'] ?? $opportunity->location,
                'employment_type' => $input['employment_type'] ?? $opportunity->employment_type,
                'is_remote' => array_key_exists('is_remote', $input) ? (bool)$input['is_remote'] : $opportunity->is_remote,
                'salary_range' => $input['salary_range'] ?? $opportunity->salary_range,
                'skills' => $input['skills'] ?? $opportunity->skills,
                'description' => $input['description'] ?? $opportunity->description,
                'requirements' => $input['requirements'] ?? $opportunity->requirements,
                'benefits' => $input['benefits'] ?? $opportunity->benefits,
            ]);

            if (isset($input['status']) && $input['status'] !== $opportunity->status) {
                $opportunity->status = $input['status'];
                $opportunity->published_at = $input['status'] === 'published' ? now() : $opportunity->published_at;
            }

            if (isset($input['title']) && $input['title'] !== $opportunity->title) {
                $opportunity->slug = Str::slug($opportunity->title.'-'.$opportunity->company_profile_id.'-'.Str::random(6));
            }

            $opportunity->save();

            return $opportunity;
        });
    }
}
