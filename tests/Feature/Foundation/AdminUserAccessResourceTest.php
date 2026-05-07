<?php

namespace Tests\Feature\Foundation;

use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminUserAccessResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_management_resources_respect_view_and_manage_permissions(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('users.view', 'web'),
            Permission::findOrCreate('roles.view', 'web'),
            Permission::findOrCreate('permissions.view', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(RoleResource::canViewAny());
        $this->assertTrue(PermissionResource::canViewAny());
        $this->assertFalse(UserResource::canCreate());
        $this->assertFalse(RoleResource::canCreate());
        $this->assertFalse(PermissionResource::canCreate());

        $role->givePermissionTo([
            Permission::findOrCreate('users.manage', 'web'),
            Permission::findOrCreate('roles.manage', 'web'),
            Permission::findOrCreate('permissions.manage', 'web'),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($user->refresh());

        $this->assertTrue(UserResource::canCreate());
        $this->assertTrue(RoleResource::canCreate());
        $this->assertTrue(PermissionResource::canCreate());
    }

    public function test_user_resource_prevents_admin_from_deleting_self(): void
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('users.view', 'web'),
            Permission::findOrCreate('users.manage', 'web'),
        ]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertFalse(UserResource::canDelete($user));
        $this->assertTrue(UserResource::canDelete($otherUser));
    }

    public function test_admin_can_open_management_resource_indexes_with_view_permissions(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('users.view', 'web'),
            Permission::findOrCreate('roles.view', 'web'),
            Permission::findOrCreate('permissions.view', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/roles')
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/permissions')
            ->assertOk();
    }
}
