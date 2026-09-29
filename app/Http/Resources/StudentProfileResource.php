<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
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
            'user_email' => $this->user->email ?? null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'profile_image_url' => $this->profile_image_url,
            'school' => $this->school,
            'school_name' => $this->school_name,
            'school_display' => $this->school_display,
            'major' => $this->major,
            'school_year' => $this->school_year,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'portfolio_url' => $this->portfolio_url,
            'cv_file_url' => $this->cv_file_url,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'cv_documents' => CvDocumentResource::collection($this->whenLoaded('cvDocuments')),
        ];
    }
}
