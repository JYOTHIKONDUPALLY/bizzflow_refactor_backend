<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Domains\Auth\Traits\HasCountry;
// use App\Domains\Auth\Traits\HasCurrency;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Franchise extends Model
{
        use HasFactory, SoftDeletes;
        // HasCountry, HasCurrency;
        protected $fillable = [
        'name',
        'industry',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    // public function franchiseAdmins(): HasMany
    // {
    //     return $this->hasMany(FranchiseAdmin::class);
    // }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }


}
