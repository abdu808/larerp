<?php

namespace Tests\Feature\Beneficiaries;

use App\Filament\Resources\Beneficiaries\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BeneficiaryFileExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_beneficiary_file_contains_daily_work_tabs(): void
    {
        $file = Family::create([
            'code' => 'BF-1001',
            'name' => 'ملف مستفيد اختبار',
            'guardian_name' => 'صاحب الملف',
            'status' => Family::STATUS_ACTIVE,
        ]);

        $beneficiary = Beneficiary::create([
            'family_id' => $file->id,
            'first_name' => 'مستفيد',
            'is_primary_contact' => true,
        ]);

        $this->actingAs($this->superAdminUser())
            ->get(BeneficiaryResource::getUrl('edit', ['record' => $beneficiary], panel: 'admin'))
            ->assertOk()
            ->assertSee('المستفيد والتابعون')
            ->assertSee('الحالات')
            ->assertSee('الوثائق');
    }

    private function superAdminUser(): User
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $user = User::factory()->create();

        $user->assignRole($role);

        return $user;
    }
}
