<?php

namespace Tests\Feature\Beneficiaries;

use App\Models\AssistanceRequest;
use App\Models\Beneficiary;
use App\Models\CaseStudy;
use App\Models\FieldVisit;
use App\Models\NeedsAssessment;
use App\Models\SocialCase;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseStudyAssessmentVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_case_study_assessment_and_visit_relationships_can_be_created(): void
    {
        [$socialCase, $researcher, $supervisor] = $this->baseRecords('SC-CASE-0001');
        $assistanceRequest = AssistanceRequest::factory()->create([
            'beneficiary_id' => $socialCase->beneficiary_id,
            'social_case_id' => $socialCase->id,
        ]);

        $caseStudy = CaseStudy::create([
            'social_case_id' => $socialCase->id,
            'assistance_request_id' => $assistanceRequest->id,
            'researcher_id' => $researcher->id,
            'supervisor_id' => $supervisor->id,
            'status' => CaseStudy::STATUS_UNDER_STUDY,
            'started_at' => '2026-05-07',
            'summary' => 'Initial social study summary.',
            'family_situation' => 'Beneficiary file situation details.',
            'risk_factors' => 'Rent arrears and unstable income.',
        ]);

        $assessment = NeedsAssessment::create([
            'case_study_id' => $caseStudy->id,
            'assessed_by_id' => $researcher->id,
            'income_score' => 20,
            'vulnerability_score' => 15,
            'housing_score' => 10,
            'health_score' => 10,
            'education_score' => 5,
            'debts_score' => 5,
            'support_sources_score' => 3,
            'assessed_at' => '2026-05-07 09:00:00',
        ]);

        $visit = FieldVisit::create([
            'case_study_id' => $caseStudy->id,
            'social_case_id' => $socialCase->id,
            'visitor_id' => $researcher->id,
            'status' => FieldVisit::STATUS_COMPLETED,
            'type' => FieldVisit::TYPE_FIELD,
            'scheduled_at' => '2026-05-07 10:00:00',
            'completed_at' => '2026-05-07 10:30:00',
            'location' => 'Riyadh',
            'purpose' => 'Verify living conditions.',
            'findings' => 'Need confirmed.',
        ]);

        $this->assertTrue($socialCase->is($caseStudy->socialCase));
        $this->assertTrue($researcher->is($caseStudy->researcher));
        $this->assertTrue($supervisor->is($caseStudy->supervisor));
        $this->assertTrue($caseStudy->needsAssessment->is($assessment));
        $this->assertTrue($caseStudy->fieldVisits->contains($visit));
        $this->assertTrue($visit->socialCase->is($socialCase));
        $this->assertTrue($assistanceRequest->is($caseStudy->assistanceRequest));
    }

    public function test_needs_assessment_calculates_total_score_and_level_from_approved_weights(): void
    {
        [$socialCase, $researcher, $supervisor] = $this->baseRecords('SC-CASE-0002');
        $caseStudy = $this->caseStudy($socialCase, $researcher, $supervisor);

        $assessment = NeedsAssessment::create([
            'case_study_id' => $caseStudy->id,
            'assessed_by_id' => $researcher->id,
            'income_score' => 25,
            'vulnerability_score' => 20,
            'housing_score' => 15,
            'health_score' => 15,
            'education_score' => 10,
            'debts_score' => 10,
            'support_sources_score' => 5,
        ]);

        $this->assertSame(100, $assessment->total_score);
        $this->assertSame(NeedsAssessment::LEVEL_CRITICAL, $assessment->level);

        $assessment->update([
            'income_score' => 10,
            'vulnerability_score' => 10,
            'housing_score' => 5,
            'health_score' => 5,
            'education_score' => 3,
            'debts_score' => 2,
            'support_sources_score' => 1,
        ]);

        $this->assertSame(36, $assessment->refresh()->total_score);
        $this->assertSame(NeedsAssessment::LEVEL_MEDIUM, $assessment->level);
    }

    public function test_case_study_cannot_be_ready_for_supervisor_without_assessment_and_recommendation(): void
    {
        [$socialCase, $researcher, $supervisor] = $this->baseRecords('SC-CASE-0003');
        $caseStudy = $this->caseStudy($socialCase, $researcher, $supervisor);

        $this->expectException(DomainException::class);

        $caseStudy->update(['status' => CaseStudy::STATUS_READY_FOR_SUPERVISOR]);
    }

    public function test_case_study_cannot_be_completed_without_recommendation_even_with_assessment(): void
    {
        [$socialCase, $researcher, $supervisor] = $this->baseRecords('SC-CASE-0004');
        $caseStudy = $this->caseStudy($socialCase, $researcher, $supervisor);

        NeedsAssessment::create([
            'case_study_id' => $caseStudy->id,
            'income_score' => 15,
            'vulnerability_score' => 10,
            'housing_score' => 5,
        ]);

        $this->expectException(DomainException::class);

        $caseStudy->update(['status' => CaseStudy::STATUS_COMPLETED]);
    }

    public function test_case_study_can_be_ready_when_assessment_and_recommendation_exist(): void
    {
        [$socialCase, $researcher, $supervisor] = $this->baseRecords('SC-CASE-0005');
        $caseStudy = $this->caseStudy($socialCase, $researcher, $supervisor);

        NeedsAssessment::create([
            'case_study_id' => $caseStudy->id,
            'income_score' => 15,
            'vulnerability_score' => 10,
            'housing_score' => 5,
        ]);

        $caseStudy->update([
            'recommendation' => 'Recommend temporary rent support.',
            'status' => CaseStudy::STATUS_READY_FOR_SUPERVISOR,
        ]);

        $this->assertSame(CaseStudy::STATUS_READY_FOR_SUPERVISOR, $caseStudy->refresh()->status);
        $this->assertNotNull($caseStudy->ready_for_supervisor_at);
    }

    /**
     * @return array{0: SocialCase, 1: User, 2: User}
     */
    private function baseRecords(string $caseNumber): array
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'Test',
            'family_name' => 'Beneficiary',
            'status' => Beneficiary::STATUS_ACTIVE,
            'registered_at' => '2026-05-07',
        ]);

        $socialCase = SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => $caseNumber,
            'summary' => 'Case summary.',
        ]);

        return [
            $socialCase,
            User::factory()->create(['email' => strtolower($caseNumber).'-researcher@example.test']),
            User::factory()->create(['email' => strtolower($caseNumber).'-supervisor@example.test']),
        ];
    }

    private function caseStudy(SocialCase $socialCase, User $researcher, User $supervisor): CaseStudy
    {
        return CaseStudy::create([
            'social_case_id' => $socialCase->id,
            'researcher_id' => $researcher->id,
            'supervisor_id' => $supervisor->id,
            'status' => CaseStudy::STATUS_UNDER_STUDY,
            'summary' => 'Study summary.',
        ]);
    }
}
