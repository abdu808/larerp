<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\FollowUp;
use App\Models\SocialCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FollowUp>
 */
class FollowUpFactory extends Factory
{
    protected $model = FollowUp::class;

    public function definition(): array
    {
        return [
            'social_case_id' => fn (): int => $this->socialCase()->id,
            'status' => FollowUp::STATUS_DONE,
            'followed_up_at' => now()->toDateString(),
            'outcome' => 'تحسن وضع الأسرة بعد تنفيذ الخدمة.',
            'improvement_level' => 'moderate',
            'follow_up_decision' => 'schedule_follow_up',
            'next_follow_up_at' => now()->addMonth()->toDateString(),
            'followed_by_id' => User::factory(),
            'notes' => null,
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
            'summary' => 'حالة لاختبار المتابعة.',
        ]);
    }
}
