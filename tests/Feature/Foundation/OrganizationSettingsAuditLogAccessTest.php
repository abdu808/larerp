<?php

namespace Tests\Feature\Foundation;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\OrganizationProfiles\OrganizationProfileResource;
use App\Models\AuditLog;
use App\Models\OrganizationProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OrganizationSettingsAuditLogAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_with_settings_view_only_cannot_manage_organization_settings(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('settings.view', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $profile = OrganizationProfile::query()->create([
            'name' => 'Test Charity',
            'settings' => ['locale' => 'ar'],
            'active_modules' => ['foundation'],
        ]);

        $this->actingAs($user);

        $this->assertTrue(OrganizationProfileResource::canViewAny());
        $this->assertFalse(OrganizationProfileResource::canCreate());
        $this->assertFalse(OrganizationProfileResource::canEdit($profile));
        $this->assertFalse(OrganizationProfileResource::canDelete($profile));

        $this->get('/admin/organization-profiles')
            ->assertOk();

        $this->get("/admin/organization-profiles/{$profile->getKey()}/edit")
            ->assertForbidden();
    }

    public function test_organization_profile_create_is_blocked_after_one_profile_exists(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('settings.view', 'web'),
            Permission::findOrCreate('settings.manage', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user);

        $this->assertTrue(OrganizationProfileResource::canCreate());

        OrganizationProfile::query()->create([
            'name' => 'Test Charity',
            'settings' => ['locale' => 'ar'],
            'active_modules' => ['foundation'],
        ]);

        $this->assertFalse(OrganizationProfileResource::canCreate());

        $this->get('/admin/organization-profiles/create')
            ->assertForbidden();
    }

    public function test_organization_profile_cannot_be_deleted_from_resource(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('settings.view', 'web'),
            Permission::findOrCreate('settings.manage', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $profile = OrganizationProfile::query()->create([
            'name' => 'Test Charity',
            'settings' => ['locale' => 'ar'],
            'active_modules' => ['foundation'],
        ]);

        $this->actingAs($user);

        $this->assertTrue(OrganizationProfileResource::canEdit($profile));
        $this->assertFalse(OrganizationProfileResource::canDelete($profile));
    }

    public function test_audit_log_resource_requires_audit_log_view_permission(): void
    {
        $role = Role::findOrCreate('Admin', 'web');
        $role->syncPermissions([
            Permission::findOrCreate('settings.view', 'web'),
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $auditLog = AuditLog::query()->create([
            'user_id' => $user->getKey(),
            'action' => 'updated',
            'summary' => 'Settings changed',
            'new_values' => ['name' => 'Test Charity'],
        ]);

        $this->actingAs($user);

        $this->assertFalse(AuditLogResource::canViewAny());
        $this->assertFalse(AuditLogResource::canView($auditLog));

        $this->get('/admin/audit-logs')
            ->assertForbidden();

        $role->givePermissionTo(Permission::findOrCreate('audit_logs.view', 'web'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($user->refresh());

        $this->assertTrue(AuditLogResource::canViewAny());
        $this->assertTrue(AuditLogResource::canView($auditLog));
    }
}
