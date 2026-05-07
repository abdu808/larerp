<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\CommitteeDecision;
use App\Models\ServiceDelivery;
use App\Models\SocialCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceDelivery>
 */
class ServiceDeliveryFactory extends Factory
{
    protected $model = ServiceDelivery::class;

    public function definition(): array
    {
        return [
            'social_case_id' => fn (): int => $this->socialCase()->id,
            'committee_decision_id' => fn (array $attributes): int => CommitteeDecision::create([
                'social_case_id' => $attributes['social_case_id'],
                'decision_type' => CommitteeDecision::TYPE_FINANCIAL,
                'status' => CommitteeDecision::STATUS_APPROVED,
                'approved_amount' => 1500,
                'reason' => 'تم اعتماد الدعم بناء على احتياج موثق.',
            ])->id,
            'delivery_type' => ServiceDelivery::TYPE_CASH,
            'status' => ServiceDelivery::STATUS_DELIVERED,
            'amount' => $this->faker->numberBetween(300, 3000),
            'quantity' => null,
            'unit' => null,
            'service_description' => 'تنفيذ دعم مالي معتمد للحالة.',
            'delivered_at' => now()->toDateString(),
            'delivered_by_id' => User::factory(),
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
            'summary' => 'حالة لاختبار تنفيذ الخدمة.',
        ]);
    }
}
