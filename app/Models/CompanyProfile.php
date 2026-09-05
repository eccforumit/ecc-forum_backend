<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
        'company_size',
        'company_description',
        'website',
        'logo_url',
        'logo_public_id',
        'address',
        'contact_first_name',
        'contact_last_name',
        'contact_phone',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function getContactFullNameAttribute(): string
    {
        return trim($this->contact_first_name.' '.$this->contact_last_name);
    }
}
