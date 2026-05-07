<?php

namespace Database\Factories;

use App\Models\Beneficiary;
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
            'outcome' => 'تحسن وضع ملف المستفيد بعد تنفيذ الخدمة.',
            'improvement_level' => 'moderate',
            'follow_up_decision' => 'schedule_follow_up',
            'next_follow_up_at' => now()->addMonth()->toDateString(),
            'followed_by_id' => User::factory(),
            'notes' => null,
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
            'summary' => 'حالة لاختبار المتابعة.',
        ]);
    }
}
