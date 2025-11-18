<?php

namespace App\Domains\Auth\DataTransferObjects;

use Spatie\LaravelData\Data;

class RegisterCustomerData extends Data
{
    public function __construct(
        public string $franchise_id,
        public string $business_unit_id,
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
        public ?string $phone = null,
        public ?bool $is_deleted = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {}
}
