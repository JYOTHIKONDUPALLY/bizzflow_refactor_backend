<?php

namespace App\Domains\Auth\Enums;

enum UserType: string
{
    case CUSTOMER = 'customer';
    case USER = 'user';
    case LOCATION_ADMIN = 'location_admin';
    case FRANCHISE_ADMIN = 'franchise_admin';

    public function getGuardName(): string
    {
        return match($this) {
            self::CUSTOMER => 'customer',
            self::USER => 'user',
            self::LOCATION_ADMIN => 'location_admin',
            self::FRANCHISE_ADMIN => 'franchise_admin',
        };
    }

    public function getModelClass(): string
    {
        return match($this) {
            self::CUSTOMER => \App\Domains\Auth\Models\Customer::class,
            self::USER => \App\Domains\Auth\Models\User::class,
            self::LOCATION_ADMIN => \App\Domains\Auth\Models\Location::class,
            self::FRANCHISE_ADMIN => \App\Domains\Auth\Models\Franchise::class,
        };
    }
}