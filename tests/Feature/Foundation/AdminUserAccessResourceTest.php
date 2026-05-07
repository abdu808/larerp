<?php

namespace Tests\Feature\Foundation;

use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
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

    public function test_non_super_admin_cannot_assign_super_admin_role_to_users(): void
    {
        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->givePermissionTo(Permission::findOrCreate('users.manage', 'web'));
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $targetUser = User::factory()->create();

        $this->actingAs($admin);

        $this->assertSame(
            [(string) $adminRole->getKey()],
            UserForm::filterAssignableRoleIds($targetUser, [
                (string) $adminRole->getKey(),
                (string) $superAdminRole->getKey(),
            ]),
        );

        $superAdmin = User::factory()->create();
        $superAdminRole->givePermissionTo(Permission::findOrCreate('users.manage', 'web'));
        $superAdmin->assignRole($superAdminRole);

        $this->actingAs($superAdmin);

        $this->assertSame(
            [(string) $adminRole->getKey(), (string) $superAdminRole->getKey()],
            UserForm::filterAssignableRoleIds($targetUser, [
                (string) $adminRole->getKey(),
                (string) $superAdminRole->getKey(),
            ]),
        );
    }

    public function test_non_super_admin_cannot_remove_existing_super_admin_role_while_editing_user(): void
    {
        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->givePermissionTo(Permission::findOrCreate('users.manage', 'web'));
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $targetUser = User::factory()->create();
        $targetUser->assignRole($superAdminRole);

        $this->actingAs($admin);

        $this->assertSame(
            [(string) $adminRole->getKey(), (string) $superAdminRole->getKey()],
            UserForm::filterAssignableRoleIds($targetUser, [(string) $adminRole->getKey()]),
        );
    }

    public function test_role_resource_protects_super_admin_and_admin_roles(): void
    {
        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->givePermissionTo(Permission::findOrCreate('roles.manage', 'web'));
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');
        $customRole = Role::findOrCreate('Operations', 'web');

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        $this->assertFalse(RoleResource::canEdit($superAdminRole));
        $this->assertFalse(RoleResource::canDelete($superAdminRole));
        $this->assertFalse(RoleResource::canDelete($adminRole));
        $this->assertTrue(RoleResource::canEdit($customRole));
        $this->assertTrue(RoleResource::canDelete($customRole));

        $superAdminRole->givePermissionTo(Permission::findOrCreate('roles.manage', 'web'));

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole($superAdminRole);

        $this->actingAs($superAdmin);

        $this->assertTrue(RoleResource::canEdit($superAdminRole));
        $this->assertFalse(RoleResource::canDelete($superAdminRole));
        $this->assertFalse(RoleResource::canDelete($adminRole));
    }

    public function test_role_and_permission_guard_name_fields_are_hidden(): void
    {
        $roleGuardField = collect(RoleForm::configure(Schema::make())->getComponents(withHidden: true))
            ->first(fn ($component): bool => $component->getName() === 'guard_name');
        $permissionGuardField = collect(PermissionForm::configure(Schema::make())->getComponents(withHidden: true))
            ->first(fn ($component): bool => $component->getName() === 'guard_name');

        $this->assertInstanceOf(Hidden::class, $roleGuardField);
        $this->assertInstanceOf(Hidden::class, $permissionGuardField);
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
