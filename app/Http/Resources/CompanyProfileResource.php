<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileResource extends JsonResource
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
            'company_name' => $this->company_name,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'company_description' => $this->company_description,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'address' => $this->address,
            'contact_first_name' => $this->contact_first_name,
            'contact_last_name' => $this->contact_last_name,
            'contact_full_name' => $this->contact_full_name,
            'contact_phone' => $this->contact_phone,
            'is_verified' => $this->is_verified,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
