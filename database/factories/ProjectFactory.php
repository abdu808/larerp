<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'code' => fake()->unique()->bothify('PRJ-####'),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['relief', 'health', 'education', 'housing']),
            'status' => fake()->randomElement(array_keys(Project::STATUSES)),
            'goal_amount' => fake()->numberBetween(10000, 100000),
            'collected_amount' => 0,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addMonths(3)->toDateString(),
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
