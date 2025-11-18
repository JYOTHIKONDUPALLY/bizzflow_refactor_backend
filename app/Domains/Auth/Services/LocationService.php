<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\LocationRepository;

class LocationService
{
    public function __construct(
        private LocationRepository $locationRepo
    ) {}

    public function getLocationsByFranchise(int $franchiseId)
    {
        return $this->locationRepo->findByFranchise($franchiseId);
    }

    public function getLocationById(int $id)
    {
        return $this->locationRepo->findById($id);
    }

    public function getLocationByCode(string $code)
    {
        return $this->locationRepo->findByCode($code);
    }
}
