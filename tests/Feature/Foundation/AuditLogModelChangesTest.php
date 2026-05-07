<?php

namespace Tests\Feature\Foundation;

use App\Models\AuditLog;
use App\Models\OrganizationProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogModelChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_changes_are_audited_with_actor(): void
    {
        $actor = User::factory()->create();
        AuditLog::query()->delete();

        $this->actingAs($actor);

        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'audited@example.test',
        ]);

        $user->update(['name' => 'Updated Name']);
        $user->delete();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $actor->id,
            'action' => 'user.created',
            'auditable_type' => User::class,
            'auditable_id' => (string) $user->id,
        ]);

        $updatedLog = AuditLog::query()
            ->where('action', 'user.updated')
            ->where('auditable_id', $user->id)
            ->firstOrFail();

        $this->assertSame($actor->id, $updatedLog->user_id);
        $this->assertSame(['name' => 'Original Name'], $updatedLog->old_values);
        $this->assertSame(['name' => 'Updated Name'], $updatedLog->new_values);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $actor->id,
            'action' => 'user.deleted',
            'auditable_type' => User::class,
            'auditable_id' => (string) $user->id,
        ]);
    }

    public function test_sensitive_user_values_are_masked_in_audit_logs(): void
    {
        $plainPassword = 'PlainSecretPassword123!';

        User::factory()->create([
            'password' => $plainPassword,
            'remember_token' => 'remember-me-token',
        ]);

        $log = AuditLog::query()
            ->where('action', 'user.created')
            ->firstOrFail();

        $this->assertSame('[masked]', $log->new_values['password']);
        $this->assertSame('[masked]', $log->new_values['remember_token']);
        $this->assertStringNotContainsString($plainPassword, $log->toJson());
        $this->assertStringNotContainsString('remember-me-token', $log->toJson());
    }

    public function test_organization_profile_changes_are_audited(): void
    {
        $profile = OrganizationProfile::query()->create([
            'name' => 'Initial Charity',
            'charity_type' => 'foundation',
        ]);

        $profile->update(['name' => 'Updated Charity']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'organization_profile.created',
            'auditable_type' => OrganizationProfile::class,
            'auditable_id' => (string) $profile->id,
        ]);

        $updatedLog = AuditLog::query()
            ->where('action', 'organization_profile.updated')
            ->where('auditable_id', $profile->id)
            ->firstOrFail();

        $this->assertSame(['name' => 'Initial Charity'], $updatedLog->old_values);
        $this->assertSame(['name' => 'Updated Charity'], $updatedLog->new_values);
    }
}
