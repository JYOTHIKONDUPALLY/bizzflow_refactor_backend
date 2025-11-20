<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\BusinessUnits;
use App\Domains\Auth\Traits\HasFranchise;
use App\Domains\Auth\Traits\HasLocation;
use App\Domains\Auth\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasFranchise, HasLocation, HasRoles, HasApiTokens;

    protected $guard = 'user';
    
    // Primary key is a UUID string
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'franchise_id',
        'business_unit_id',
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
  public function canAccessLocation(string $businessUnitId): bool
    {
        return $this->business_unit_id === $businessUnitId;
    }

   public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnits::class);
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('name', $roleSlug)->exists();
    }
    public function getPrimaryRole(): ?Role
    {
        return $this->roles()->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // ensure UUID primary key is set
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }

            // id is set above; let Eloquent persist the model normally
        });
    }

}
