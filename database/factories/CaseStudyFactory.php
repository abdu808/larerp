<?php

namespace Database\Factories;

use App\Models\CaseStudy;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseStudy>
 */
class CaseStudyFactory extends Factory
{
    protected $model = CaseStudy::class;

    public function definition(): array
    {
        return [
            'social_case_id' => SocialCase::query()->create([
                'family_id' => Family::query()->create([
                    'code' => fake()->unique()->bothify('FAM-####'),
                    'name' => fake()->lastName().' family',
                    'guardian_name' => fake()->name(),
                ])->id,
                'case_number' => fake()->unique()->bothify('SC-2026-####'),
                'summary' => fake()->paragraph(),
            ])->id,
            'researcher_id' => User::factory(),
            'supervisor_id' => User::factory(),
            'status' => CaseStudy::STATUS_DRAFT,
            'started_at' => now()->toDateString(),
            'summary' => fake()->paragraph(),
            'family_situation' => fake()->paragraph(),
            'risk_factors' => fake()->sentence(),
        ];
    }
}
