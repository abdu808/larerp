<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Donation> */
class DonationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'campaign_id' => null,
            'donor_name' => fake()->name(),
            'donor_email' => fake()->safeEmail(),
            'donor_phone' => fake()->phoneNumber(),
            'amount' => fake()->numberBetween(50, 5000),
            'currency' => 'SAR',
            'payment_status' => fake()->randomElement(array_keys(Donation::PAYMENT_STATUSES)),
            'payment_method' => fake()->randomElement(array_keys(Donation::PAYMENT_METHODS)),
            'reference' => fake()->unique()->bothify('DON-########'),
            'donated_at' => now(),
            'notes' => null,
        ];
    }

    public function forCampaign(?Campaign $campaign = null): static
    {
        return $this->state(function () use ($campaign): array {
            $campaign ??= Campaign::factory()->create();

            return [
                'project_id' => $campaign->project_id,
                'campaign_id' => $campaign->id,
            ];
        });
    }
}
