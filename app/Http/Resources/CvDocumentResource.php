<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'summary' => $this->summary,
            'skills' => $this->skills,
            'work_experiences' => $this->work_experiences,
            'educations' => $this->educations,
            'languages' => $this->languages,
            'website' => $this->website,
            'linkedin' => $this->linkedin,
            'github' => $this->github,
            'twitter' => $this->twitter,
            'profile_slug' => $this->profile_slug,
            'profile_url' => $this->profile_url,
            'qr_code_url' => $this->qr_code_url,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'student_profile' => StudentProfileResource::make($this->whenLoaded('studentProfile')),
        ];
    }
}
