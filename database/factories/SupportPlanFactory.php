<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\SocialCase;
use App\Models\SupportPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportPlan>
 */
class SupportPlanFactory extends Factory
{
    protected $model = SupportPlan::class;

    public function definition(): array
    {
        return [
            'social_case_id' => fn (): int => $this->socialCase()->id,
            'plan_type' => SupportPlan::TYPE_RELIEF,
            'goal' => 'تغطية الاحتياج الأساسي لملف المستفيد خلال مدة الخطة.',
            'status' => SupportPlan::STATUS_ACTIVE,
            'owner_id' => User::factory(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'success_criteria' => 'استقرار المصروفات الأساسية وعدم وجود احتياج عاجل جديد.',
        ];
    }

    private function socialCase(): SocialCase
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'مستفيد',
            'family_name' => 'اختبار',
            'status' => Beneficiary::STATUS_ACTIVE,
            'registered_at' => now()->toDateString(),
        ]);

        return SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-'.$this->faker->unique()->numerify('####'),
            'summary' => 'حالة لاختبار خطة الدعم.',
        ]);
    }
}
