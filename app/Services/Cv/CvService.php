<?php

namespace App\Services\Cv;

use App\Models\CvDocument;
use App\Models\StudentProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CvService
{
    public function store(StudentProfile $profile, array $input): CvDocument
    {
        return DB::transaction(function () use ($profile, $input) {
            $slug = $this->makeSlug($profile, $input['title']);

            $document = CvDocument::create([
                'student_profile_id' => $profile->id,
                'title' => $input['title'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
                'location' => $input['location'] ?? null,
                'summary' => $input['summary'] ?? null,
                'skills' => $input['skills'] ?? [],
                'work_experiences' => $input['work_experiences'] ?? [],
                'educations' => $input['educations'] ?? [],
                'languages' => $input['languages'] ?? [],
                'website' => $input['website'] ?? null,
                'linkedin' => $input['linkedin'] ?? null,
                'github' => $input['github'] ?? null,
                'twitter' => $input['twitter'] ?? null,
                'profile_slug' => $slug,
                'profile_url' => sprintf('/cv/%s', $slug),
            ]);

            return $document;
        });
    }

    public function update(CvDocument $document, array $input): CvDocument
    {
        return DB::transaction(function () use ($document, $input) {
            $document->fill([
                'title' => $input['title'] ?? $document->title,
                'first_name' => $input['first_name'] ?? $document->first_name,
                'last_name' => $input['last_name'] ?? $document->last_name,
                'email' => $input['email'] ?? $document->email,
                'phone' => $input['phone'] ?? $document->phone,
                'location' => $input['location'] ?? $document->location,
                'summary' => $input['summary'] ?? $document->summary,
                'skills' => $input['skills'] ?? $document->skills,
                'work_experiences' => $input['work_experiences'] ?? $document->work_experiences,
                'educations' => $input['educations'] ?? $document->educations,
                'languages' => $input['languages'] ?? $document->languages,
                'website' => $input['website'] ?? $document->website,
                'linkedin' => $input['linkedin'] ?? $document->linkedin,
                'github' => $input['github'] ?? $document->github,
                'twitter' => $input['twitter'] ?? $document->twitter,
            ]);

            if (isset($input['title']) && $input['title'] !== $document->title) {
                $document->profile_slug = $this->makeSlug($document->studentProfile, $document->title);
                $document->profile_url = sprintf('/cv/%s', $document->profile_slug);
            }

            $document->save();

            return $document;
        });
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = CvDocument::query()->with('studentProfile.user');

        if (!empty($filters['school'])) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('school', $filters['school']));
        }

        if (!empty($filters['major'])) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('major', $filters['major']));
        }

        if (!empty($filters['skill'])) {
            $query->whereJsonContains('skills', [['name' => $filters['skill']]]);
        }

        if (!empty($filters['keyword'])) {
            $keyword = '%'.$filters['keyword'].'%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('summary', 'like', $keyword)
                    ->orWhere('skills', 'like', $keyword);
            });
        }

        $perPage = (int)($filters['per_page'] ?? 15);

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    protected function makeSlug(StudentProfile $profile, string $title): string
    {
        return Str::slug($title.'-'.$profile->id.'-'.Str::random(6));
    }
}
