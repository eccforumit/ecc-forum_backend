<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvDocument extends Model
{
    /** @use HasFactory<\Database\Factories\CvDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'location',
        'summary',
        'skills',
        'work_experiences',
        'educations',
        'languages',
        'website',
        'linkedin',
        'github',
        'twitter',
        'profile_slug',
        'profile_url',
        'qr_code_url',
    ];

    protected $casts = [
        'skills' => 'array',
        'work_experiences' => 'array',
        'educations' => 'array',
        'languages' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function getRouteKeyName(): string
    {
        return 'profile_slug';
    }
}
