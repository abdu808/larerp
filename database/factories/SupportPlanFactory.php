<?php

namespace Database\Factories;

use App\Models\Family;
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
            'goal' => 'تغطية الاحتياج الأساسي للأسرة خلال مدة الخطة.',
            'status' => SupportPlan::STATUS_ACTIVE,
            'owner_id' => User::factory(),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'success_criteria' => 'استقرار المصروفات الأساسية وعدم وجود احتياج عاجل جديد.',
        ];
    }

    private function socialCase(): SocialCase
    {
        $family = Family::create([
            'code' => 'FAM-'.$this->faker->unique()->numerify('####'),
            'name' => 'أسرة اختبار',
            'guardian_name' => 'رب الأسرة',
        ]);

        return SocialCase::create([
            'family_id' => $family->id,
            'case_number' => 'SC-'.$this->faker->unique()->numerify('####'),
            'summary' => 'حالة لاختبار خطة الدعم.',
        ]);
    }
}
