<?php

namespace Database\Factories;

use App\Models\AssistanceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssistanceRequest>
 */
class AssistanceRequestFactory extends Factory
{
    protected $model = AssistanceRequest::class;

    public function definition(): array
    {
        return [
            'request_number' => 'AR-'.$this->faker->unique()->numerify('2026-####'),
            'request_type' => $this->faker->randomElement(array_keys(AssistanceRequest::TYPE_OPTIONS)),
            'status' => AssistanceRequest::STATUS_DRAFT,
            'urgency' => 'normal',
            'source' => 'office',
            'description' => $this->faker->sentence(),
            'consent_to_store_data' => true,
        ];
    }
}
