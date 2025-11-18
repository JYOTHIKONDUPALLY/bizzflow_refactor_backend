<?php

namespace App\Domains\Auth\DataTransferObjects;

use Spatie\LaravelData\Data;

class RegisterLocationAdminData extends Data
{
    public function __construct(
        public int $franchise_id,
        public int $location_id,
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
        public ?string $phone,
    ) {}
}
