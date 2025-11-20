<?php 
namespace Domain\Auth\DTOs;

class AuthTokenDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
        public readonly array $user,
        public readonly string $role,
        public readonly string $franchiseId,
        public readonly string $businessUnitId,
    ) {}

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'user' => $this->user,
            'role' => $this->role,
            'franchise_id' => $this->franchiseId,
            'business_unit_id' => $this->businessUnitId,
        ];
    }
}