<?php

namespace Database\Factories;

use App\Models\CaseStudy;
use App\Models\NeedsAssessment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NeedsAssessment>
 */
class NeedsAssessmentFactory extends Factory
{
    protected $model = NeedsAssessment::class;

    public function definition(): array
    {
        return [
            'case_study_id' => CaseStudy::factory(),
            'assessed_by_id' => User::factory(),
            'income_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['income_score']),
            'vulnerability_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['vulnerability_score']),
            'housing_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['housing_score']),
            'health_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['health_score']),
            'education_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['education_score']),
            'debts_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['debts_score']),
            'support_sources_score' => fake()->numberBetween(0, NeedsAssessment::WEIGHTS['support_sources_score']),
            'notes' => fake()->sentence(),
            'assessed_at' => now(),
        ];
    }
}
