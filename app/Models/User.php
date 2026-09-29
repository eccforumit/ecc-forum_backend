<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_type',
        'first_name',
        'last_name',
        'phone_number',
        'is_email_verified',
        'is_active',
        'is_staff',
        'email_verification_token',
        'password_reset_token',
        'password_reset_expires',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
        'password_reset_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_email_verified' => 'boolean',
            'is_active' => 'boolean',
            'is_staff' => 'boolean',
            'password_reset_expires' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    /**
     * Determine if the user is a company.
     */
    public function isCompany(): bool
    {
        return $this->user_type === 'company';
    }

    /**
     * Relation with the student profile.
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Relation with the company profile.
     */
    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }
}
