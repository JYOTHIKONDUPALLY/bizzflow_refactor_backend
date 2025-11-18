<?php

namespace App\Domains\Auth\Traits;

use App\Domains\Auth\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasRoles
{
    public function Role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

   public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        $permissions = $this->role->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}