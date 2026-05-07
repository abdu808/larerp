<?php

namespace Database\Factories;

use App\Models\CommitteeDecision;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommitteeDecision>
 */
class CommitteeDecisionFactory extends Factory
{
    protected $model = CommitteeDecision::class;

    public function definition(): array
    {
        return [
            'social_case_id' => fn (): int => $this->socialCase()->id,
            'decision_type' => CommitteeDecision::TYPE_FINANCIAL,
            'status' => CommitteeDecision::STATUS_APPROVED,
            'approved_amount' => $this->faker->numberBetween(500, 5000),
            'approved_service_type' => null,
            'effective_from' => now()->toDateString(),
            'effective_to' => now()->addMonths(3)->toDateString(),
            'reason' => 'تم اعتماد الدعم بناء على احتياج موثق.',
            'decided_by_id' => User::factory(),
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
            'summary' => 'حالة لاختبار قرار اللجنة.',
        ]);
    }
}
