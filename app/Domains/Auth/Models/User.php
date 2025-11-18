<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Traits\HasFranchise;
use App\Domains\Auth\Traits\HasLocation;
use App\Domains\Auth\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, HasFranchise, HasLocation, HasRoles;

    protected $guard = 'user';

    protected $fillable = [
        'franchise_id',
        'business_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'is_active',
        'is_deleted',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'last_login',
        'failed_login_attempts',
        'locked_until',
    ];

    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    protected $casts = [
        // 'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // User has access ONLY to their assigned location
    public function canAccessLocation(int $locationId): bool
    {
        return $this->location_id === $locationId;
    }
}
