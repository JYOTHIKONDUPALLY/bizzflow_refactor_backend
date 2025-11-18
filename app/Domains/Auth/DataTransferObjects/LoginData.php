<?php

namespace App\Domains\Auth\DataTransferObjects;

use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $franchise_id = null,
        public ?string $business_unit_id = null,
        public ?string $location_id = null,
    ) {}
}
