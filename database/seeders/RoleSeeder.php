<?php
namespace Database\Seeders;

use App\Domains\Auth\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'BU Admin',
                'description' => 'Business Unit Administrator',
                'franchise_id'=>'ce8017b4-bee1-11f0-8a5a-1098191dd2de'
            ],
            [
                'name' => 'BU Manager',
                'description' => 'Business Unit Manager',
                'franchise_id'=>'ce8017b4-bee1-11f0-8a5a-1098191dd2de'
            ],
            [
                'name' => 'BU Staff',
                'description' => 'Business Unit Staff',
                'franchise_id'=>'ce8017b4-bee1-11f0-8a5a-1098191dd2de'
            ],
        ];

        foreach ($roles as $role) {
            Role::create([
                'id' => Str::uuid(),
                'name' => $role['name'],
                'franchise_id' => $role['franchise_id'],
                'description' => $role['description'],
                'created_at' => now(),
            ]);
        }
    }
}