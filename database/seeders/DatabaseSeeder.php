<?php

namespace Database\Seeders;

use App\Models\OrganizationProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'foundation.view',
            'foundation.manage',
            'users.view',
            'users.manage',
            'settings.view',
            'settings.manage',
            'audit_logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $admin = Role::findOrCreate('Admin', 'web');

        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions([
            'foundation.view',
            'users.view',
            'settings.view',
            'audit_logs.view',
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@larerp.local'],
            [
                'name' => 'LarERP Admin',
                'password' => Hash::make('password'),
            ],
        );

        $user->assignRole($superAdmin);

        OrganizationProfile::query()->firstOrCreate([
            'name' => 'جمعية تجريبية',
        ], [
            'legal_name' => 'جمعية تجريبية للتطوير',
            'charity_type' => 'general_charity',
            'email' => 'info@larerp.local',
            'phone' => '+966500000000',
            'active_modules' => [
                'foundation',
                'beneficiaries',
                'projects',
                'donations',
                'finance',
                'governance',
            ],
            'settings' => [
                'locale' => 'ar',
                'timezone' => 'Asia/Riyadh',
            ],
        ]);
    }
}
