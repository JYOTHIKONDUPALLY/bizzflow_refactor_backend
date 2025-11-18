<?php

namespace App\Domains\Auth\DataTransferObjects;

use Spatie\LaravelData\Data;

class AuthResponseData extends Data
{
    public function __construct(
        public string $token,
        public string $type,
        public array $user,
        public ?array $franchise = null,
        public ?array $location = null,
        public ?array $accessible_locations = null,
    ) {}
}
