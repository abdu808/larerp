<?php

namespace Tests\Feature\Beneficiaries;

use App\Filament\Pages\BeneficiariesOperationsDashboard;
use App\Models\Beneficiary;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BeneficiaryOperationsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_authorized_user_can_access_beneficiaries_operations_dashboard(): void
    {
        $user = $this->superAdminUser();

        $this->actingAs($user)
            ->get(BeneficiariesOperationsDashboard::getUrl(panel: 'admin'))
            ->assertOk()
            ->assertSee('لوحة تشغيل المستفيدين')
            ->assertSee('مؤشرات التشغيل');
    }

    public function test_dashboard_renders_available_mvp_indicators_without_optional_tables(): void
    {
        $family = Family::create([
            'code' => 'FAM-OPS-1',
            'name' => 'أسرة اختبار التشغيل',
            'guardian_name' => 'رب الأسرة',
            'status' => Family::STATUS_ACTIVE,
        ]);

        $beneficiary = Beneficiary::create([
            'family_id' => $family->id,
            'first_name' => 'مستفيد',
        ]);

        SocialCase::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-OPS-1',
            'status' => SocialCase::STATUS_OPEN,
            'priority' => 'urgent',
            'summary' => 'حالة عاجلة لاختبار لوحة التشغيل.',
        ]);

        $this->actingAs($this->superAdminUser())
            ->get(BeneficiariesOperationsDashboard::getUrl(panel: 'admin'))
            ->assertOk()
            ->assertSee('ملفات نشطة')
            ->assertSee('مستفيدون')
            ->assertSee('حالات مفتوحة')
            ->assertSee('حالات عاجلة/عالية')
            ->assertSee('طلبات بانتظار فرز')
            ->assertSee('زيارات اليوم')
            ->assertSee('قرارات بانتظار تنفيذ')
            ->assertSee('متابعات متأخرة');
    }

    private function superAdminUser(): User
    {
        $role = Role::findOrCreate('Super Admin', 'web');
        $user = User::factory()->create();

        $user->assignRole($role);

        return $user;
    }
}
