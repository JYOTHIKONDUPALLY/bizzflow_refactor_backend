<?php

namespace App\Domains\Auth\DataTransferObjects;

use Spatie\LaravelData\Data;

class RegisterUserData extends Data
{
    public function __construct(
        public string $franchise_id,
        public string $business_unit_id,
        public string $role_id,
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
    ) {}
}
