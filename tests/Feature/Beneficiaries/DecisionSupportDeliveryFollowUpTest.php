<?php

namespace Tests\Feature\Beneficiaries;

use App\Models\AssistanceRequest;
use App\Models\Beneficiary;
use App\Models\CommitteeDecision;
use App\Models\FollowUp;
use App\Models\ServiceDelivery;
use App\Models\SocialCase;
use App\Models\SupportPlan;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DecisionSupportDeliveryFollowUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_decision_plan_delivery_and_follow_up_relationships_can_be_created(): void
    {
        $socialCase = $this->createSocialCase('SC-2026-D001');
        $assistanceRequest = AssistanceRequest::create([
            'beneficiary_id' => $socialCase->beneficiary_id,
            'social_case_id' => $socialCase->id,
            'request_number' => 'REQ-2026-D001',
            'request_type' => 'financial',
            'status' => AssistanceRequest::STATUS_PENDING_COMMITTEE,
            'urgency' => 'high',
            'description' => 'طلب مرتبط بقرار لجنة في اختبار النطاق.',
            'consent_to_store_data' => true,
        ]);
        $user = User::factory()->create();

        $decision = CommitteeDecision::create([
            'social_case_id' => $socialCase->id,
            'assistance_request_id' => $assistanceRequest->id,
            'decision_type' => CommitteeDecision::TYPE_MIXED,
            'status' => CommitteeDecision::STATUS_APPROVED,
            'approved_amount' => 1500,
            'approved_service_type' => 'سلة غذائية',
            'effective_from' => '2026-05-07',
            'effective_to' => '2026-08-07',
            'reason' => 'احتياج ملف المستفيد مثبت وتوصية الدعم مناسبة.',
            'decided_by_id' => $user->id,
        ]);

        $plan = SupportPlan::create([
            'social_case_id' => $socialCase->id,
            'plan_type' => SupportPlan::TYPE_RELIEF,
            'goal' => 'تثبيت احتياج ملف المستفيد الأساسي خلال ثلاثة أشهر.',
            'status' => SupportPlan::STATUS_ACTIVE,
            'owner_id' => $user->id,
            'start_date' => '2026-05-08',
            'end_date' => '2026-08-08',
            'success_criteria' => 'انتظام الغذاء والمصاريف الأساسية دون طلب طارئ جديد.',
        ]);

        $delivery = ServiceDelivery::create([
            'social_case_id' => $socialCase->id,
            'support_plan_id' => $plan->id,
            'committee_decision_id' => $decision->id,
            'delivery_type' => ServiceDelivery::TYPE_CASH,
            'status' => ServiceDelivery::STATUS_DELIVERED,
            'amount' => 1500,
            'service_description' => 'صرف دعم مالي بسيط مرتبط بقرار اللجنة.',
            'delivered_at' => '2026-05-09',
            'delivered_by_id' => $user->id,
        ]);

        $followUp = FollowUp::create([
            'social_case_id' => $socialCase->id,
            'support_plan_id' => $plan->id,
            'service_delivery_id' => $delivery->id,
            'status' => FollowUp::STATUS_DONE,
            'followed_up_at' => '2026-06-09',
            'outcome' => 'تمت ملاحظة تحسن في تغطية الاحتياجات الأساسية.',
            'improvement_level' => 'moderate',
            'follow_up_decision' => 'schedule_follow_up',
            'next_follow_up_at' => '2026-07-09',
            'followed_by_id' => $user->id,
        ]);

        $this->assertTrue($socialCase->is($decision->socialCase));
        $this->assertTrue($assistanceRequest->is($decision->assistanceRequest));
        $this->assertTrue($user->is($decision->decidedBy));
        $this->assertTrue($socialCase->is($plan->socialCase));
        $this->assertTrue($user->is($plan->owner));
        $this->assertTrue($plan->serviceDeliveries->contains($delivery));
        $this->assertTrue($decision->serviceDeliveries->contains($delivery));
        $this->assertTrue($delivery->followUps->contains($followUp));
        $this->assertTrue($plan->followUps->contains($followUp));
        $this->assertSame(CommitteeDecision::STATUS_APPROVED, $decision->status);
        $this->assertSame(SupportPlan::STATUS_ACTIVE, $plan->status);
        $this->assertSame(ServiceDelivery::STATUS_DELIVERED, $delivery->status);
        $this->assertSame(FollowUp::STATUS_DONE, $followUp->status);
    }

    public function test_approved_committee_decision_requires_reason(): void
    {
        $this->expectException(DomainException::class);

        CommitteeDecision::create([
            'social_case_id' => $this->createSocialCase('SC-2026-D002')->id,
            'decision_type' => CommitteeDecision::TYPE_FINANCIAL,
            'status' => CommitteeDecision::STATUS_APPROVED,
            'approved_amount' => 1000,
        ]);
    }

    public function test_service_delivery_rejects_non_positive_amount_or_quantity_when_provided(): void
    {
        $socialCase = $this->createSocialCase('SC-2026-D003');
        $decision = CommitteeDecision::create([
            'social_case_id' => $socialCase->id,
            'decision_type' => CommitteeDecision::TYPE_SERVICE,
            'status' => CommitteeDecision::STATUS_APPROVED,
            'approved_service_type' => 'سلة غذائية',
            'reason' => 'احتياج عيني معتمد.',
        ]);

        $this->expectException(DomainException::class);

        ServiceDelivery::create([
            'social_case_id' => $socialCase->id,
            'committee_decision_id' => $decision->id,
            'delivery_type' => ServiceDelivery::TYPE_IN_KIND,
            'status' => ServiceDelivery::STATUS_SCHEDULED,
            'quantity' => 0,
            'unit' => 'سلة',
            'service_description' => 'كمية غير منطقية يجب رفضها.',
        ]);
    }

    public function test_service_delivery_requires_plan_or_decision_reference(): void
    {
        $this->expectException(DomainException::class);

        ServiceDelivery::create([
            'social_case_id' => $this->createSocialCase('SC-2026-D004')->id,
            'delivery_type' => ServiceDelivery::TYPE_SERVICE,
            'status' => ServiceDelivery::STATUS_SCHEDULED,
            'service_description' => 'تنفيذ بلا خطة أو قرار يجب رفضه.',
        ]);
    }

    private function createSocialCase(string $caseNumber): SocialCase
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'مستفيد',
            'family_name' => 'اختبار',
            'status' => Beneficiary::STATUS_ACTIVE,
            'registered_at' => '2026-05-07',
        ]);

        return SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => $caseNumber,
            'summary' => 'حالة اختبارية لنطاق القرار والخطة والتنفيذ والمتابعة.',
        ]);
    }
}
