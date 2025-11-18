<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Traits\HasFranchise;
use App\Domains\Auth\Traits\HasLocation;
use Illuminate\Support\Str;
// use App\Domains\Auth\Traits\HasCountry;
// use App\Domains\Auth\Traits\HasCurrency;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable, HasFranchise, HasLocation;
    // HasCountry, HasCurrency;

    protected $guard = 'customer';

    // Primary key is a UUID string, not an auto-incrementing integer
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'franchise_id',
        'business_unit_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'is_deleted',
        'created_at',
        'updated_at',
        'password_hash'
    ];


    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Customer belongs to specific franchise and location
    public function canAccessLocation(string $businessUnitId): bool
    {
        return $this->business_unit_id === $businessUnitId;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // ensure UUID primary key is set
            if (empty($customer->id)) {
                $customer->id = (string) Str::uuid();
            }

            if (empty($customer->customer_code)) {
                $customer->customer_code = 'CUST' . str_pad($customer->business_unit_id, 3, '0', STR_PAD_LEFT)
                    . str_pad(Customer::where('business_unit_id', $customer->business_unit_id)->count() + 1, 5, '0', STR_PAD_LEFT);
            }

            // id and customer_code are set above; let Eloquent persist the model normally
        });
    }
}
