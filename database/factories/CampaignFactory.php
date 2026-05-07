<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Campaign> */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(3),
            'code' => fake()->unique()->bothify('CMP-####'),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(array_keys(Campaign::STATUSES)),
            'goal_amount' => fake()->numberBetween(5000, 50000),
            'collected_amount' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'channel' => fake()->randomElement(array_keys(Campaign::CHANNELS)),
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
