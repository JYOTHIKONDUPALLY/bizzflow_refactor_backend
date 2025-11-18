<?php

namespace App\Domains\Auth\Enums;

enum UserRole: string
{
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case RESOURCE = 'resource';

    public function getPermissions(): array
    {
        return match($this) {
            self::MANAGER => [
                'view_dashboard',
                'manage_staff',
                'manage_customers',
                'manage_bookings',
                'view_reports',
                'manage_inventory',// edit later
            ],
            self::STAFF => [
                'view_dashboard',
                'view_customers',
                'manage_bookings',
                'view_own_schedule',// edit later
            ],
            self::RESOURCE => [
                'view_own_schedule',
                'update_booking_status',// edit later
            ],
        };
    }
}