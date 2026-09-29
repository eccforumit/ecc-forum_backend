<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    /** @use HasFactory<\Database\Factories\OpportunityFactory> */
    use HasFactory;

    protected $fillable = [
        'company_profile_id',
        'title',
        'slug',
        'location',
        'employment_type',
        'is_remote',
        'salary_range',
        'skills',
        'description',
        'requirements',
        'benefits',
        'status',
        'published_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_remote' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
