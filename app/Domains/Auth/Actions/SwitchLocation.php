<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\FranchiseAdmin;
use App\Domains\Auth\Events\LocationSwitched;
use Illuminate\Validation\ValidationException;

class SwitchLocationAction
{
    public function execute(FranchiseAdmin $admin, int $locationId): void
    {
        if (!$admin->canAccessLocation($locationId)) {
            throw ValidationException::withMessages([
                'location_id' => ['You do not have access to this location.'],
            ]);
        }

        $admin->update([
            'last_login_location_id' => $locationId,
        ]);

        event(new LocationSwitched($admin, $locationId));
    }
}
