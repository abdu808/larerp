<?php

namespace Tests\Feature\Beneficiaries;

use App\Models\AssistanceRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistanceRequestDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assistance_request_and_document_relationships_can_be_created(): void
    {
        $family = Family::create([
            'code' => 'FAM-2001',
            'name' => 'أسرة طلب خدمة',
            'guardian_name' => 'رب الأسرة',
        ]);

        $beneficiary = Beneficiary::create([
            'family_id' => $family->id,
            'first_name' => 'مستفيد',
            'family_name' => 'اختبار',
        ]);

        $socialCase = SocialCase::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-2001',
            'summary' => 'حالة مرتبطة بطلب خدمة.',
        ]);

        $assignedTo = User::factory()->create();
        $uploadedBy = User::factory()->create();
        $verifiedBy = User::factory()->create();

        $request = AssistanceRequest::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'social_case_id' => $socialCase->id,
            'assigned_to_id' => $assignedTo->id,
            'request_number' => 'AR-2026-2001',
            'request_type' => 'financial',
            'status' => AssistanceRequest::STATUS_SUBMITTED,
            'urgency' => 'high',
            'source' => 'office',
            'submitted_at' => '2026-05-07 09:00:00',
            'description' => 'طلب دعم مالي أولي.',
            'consent_to_store_data' => true,
        ]);

        $document = BeneficiaryDocument::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'social_case_id' => $socialCase->id,
            'assistance_request_id' => $request->id,
            'uploaded_by_id' => $uploadedBy->id,
            'verified_by_id' => $verifiedBy->id,
            'title' => 'إثبات دخل',
            'file_path' => 'beneficiary-documents/income.pdf',
            'document_type' => 'income_statement',
            'sensitivity_level' => 'confidential',
            'verification_status' => BeneficiaryDocument::STATUS_VERIFIED,
            'issued_on' => '2026-04-01',
            'expires_on' => '2027-04-01',
            'verified_at' => '2026-05-07 10:00:00',
        ]);

        $this->assertTrue($family->is($request->family));
        $this->assertTrue($beneficiary->is($request->beneficiary));
        $this->assertTrue($socialCase->is($request->socialCase));
        $this->assertTrue($assignedTo->is($request->assignedTo));
        $this->assertTrue($request->documents->contains($document));
        $this->assertTrue($uploadedBy->is($document->uploadedBy));
        $this->assertTrue($verifiedBy->is($document->verifiedBy));
        $this->assertTrue($request->isSubmitted());
    }

    public function test_assistance_request_status_labels_colors_and_closed_states_are_clear(): void
    {
        $this->assertSame('مسودة', AssistanceRequest::statusLabelFor(AssistanceRequest::STATUS_DRAFT));
        $this->assertSame('success', AssistanceRequest::statusColorFor(AssistanceRequest::STATUS_APPROVED));
        $this->assertSame('danger', AssistanceRequest::urgencyColorFor('urgent'));

        $request = AssistanceRequest::factory()->create([
            'status' => AssistanceRequest::STATUS_REJECTED,
        ]);

        $this->assertTrue($request->isClosed());
    }

    public function test_beneficiary_document_used_in_decision_cannot_be_deleted(): void
    {
        $document = BeneficiaryDocument::factory()->create([
            'used_in_decision_at' => now(),
        ]);

        $this->expectException(DomainException::class);

        $document->delete();
    }

    public function test_expired_beneficiary_document_is_marked_expired(): void
    {
        $document = BeneficiaryDocument::factory()->create([
            'verification_status' => BeneficiaryDocument::STATUS_VERIFIED,
            'expires_on' => now()->subDay()->toDateString(),
        ]);

        $this->assertTrue($document->refresh()->isExpired());
        $this->assertSame(BeneficiaryDocument::STATUS_EXPIRED, $document->verification_status);
        $this->assertSame('danger', BeneficiaryDocument::verificationStatusColorFor($document->verification_status));
    }
}
