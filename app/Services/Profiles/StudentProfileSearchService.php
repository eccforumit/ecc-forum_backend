<?php

namespace App\Services\Profiles;

use App\Models\StudentProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentProfileSearchService
{
    public function searchProfiles(Request $request): LengthAwarePaginator
    {
        $query = $this->buildSearchQuery($request);

        return $query->paginate(20);
    }

    private function buildSearchQuery(Request $request): Builder
    {
        $query = StudentProfile::query()->with(['user']);

        $this->applySearchFilter($query, $request);
        $this->applyMajorFilter($query, $request);
        $this->applySchoolFilter($query, $request);
        $this->applySchoolYearFilter($query, $request);
        $this->applySkillsFilter($query, $request);
        $this->applyAvailabilityFilter($query, $request);

        return $query;
    }

    private function applySearchFilter(Builder $query, Request $request): void
    {
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function (Builder $q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('major', 'like', "%{$search}%")
                  ->orWhere('school', 'like', "%{$search}%");
            });
        }
    }

    private function applyMajorFilter(Builder $query, Request $request): void
    {
        if ($request->has('major')) {
            $query->where('major', $request->get('major'));
        }
    }

    private function applySchoolFilter(Builder $query, Request $request): void
    {
        if ($request->has('school')) {
            $query->where('school', $request->get('school'));
        }
    }

    private function applySchoolYearFilter(Builder $query, Request $request): void
    {
        if ($request->has('school_year')) {
            $query->where('school_year', $request->get('school_year'));
        }
    }

    private function applySkillsFilter(Builder $query, Request $request): void
    {
        if ($request->has('skills')) {
            $skills = explode(',', $request->get('skills'));
            foreach ($skills as $skill) {
                $query->whereJsonContains('skills', trim($skill));
            }
        }
    }

    private function applyAvailabilityFilter(Builder $query, Request $request): void
    {
        if ($request->has('availability')) {
            $query->where('availability', $request->get('availability'));
        }
    }
}
