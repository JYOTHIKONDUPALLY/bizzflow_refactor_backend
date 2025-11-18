<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Traits\HasFranchise;
// use App\Domains\Auth\Traits\HasCountry;
// use App\Domains\Auth\Traits\HasCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, SoftDeletes, HasFranchise;
    // , HasCountry, HasCurrency;

    protected $fillable = [
        'franchise_id',
        'name',
        'code',
        'email',
        'phone',
        'country_id',
        'currency_id',
        'timezone',
        'address_id',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    // public function locationAdmins(): HasMany
    // {
    //     return $this->hasMany(LocationAdmin::class);
    // }
}
