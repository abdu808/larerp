<?php

namespace Database\Factories;

use App\Models\CaseStudy;
use App\Models\FieldVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FieldVisit>
 */
class FieldVisitFactory extends Factory
{
    protected $model = FieldVisit::class;

    public function definition(): array
    {
        $caseStudy = CaseStudy::factory()->create();

        return [
            'case_study_id' => $caseStudy->id,
            'social_case_id' => $caseStudy->social_case_id,
            'visitor_id' => User::factory(),
            'status' => FieldVisit::STATUS_SCHEDULED,
            'type' => FieldVisit::TYPE_FIELD,
            'scheduled_at' => now()->addDay(),
            'location' => fake()->city(),
            'purpose' => fake()->sentence(),
        ];
    }
}
