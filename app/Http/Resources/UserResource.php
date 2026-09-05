<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'user_type' => $this->user_type,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'is_email_verified' => $this->is_email_verified,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'student_profile' => StudentProfileResource::make($this->whenLoaded('studentProfile')),
            'company_profile' => CompanyProfileResource::make($this->whenLoaded('companyProfile')),
        ];
    }
}
