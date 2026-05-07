<?php

namespace Tests\Feature\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\SocialCase;
use App\Models\SocialCaseAttachment;
use App\Models\SocialCaseNote;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialCaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_beneficiary_file_dependents_and_social_case_relationships_can_be_created(): void
    {
        $fileOwner = Beneficiary::create([
            'national_id' => '1000000000',
            'first_name' => 'أحمد',
            'father_name' => 'محمد',
            'family_name' => 'الخالد',
            'phone' => '0500000000',
            'city' => 'الرياض',
            'district' => 'النسيم',
            'status' => Beneficiary::STATUS_ACTIVE,
            'registered_at' => '2026-05-07',
            'is_primary_contact' => true,
        ]);

        $dependent = Beneficiary::create([
            'file_owner_id' => $fileOwner->id,
            'national_id' => '1000000001',
            'first_name' => 'سارة',
            'father_name' => 'أحمد',
            'family_name' => 'الخالد',
            'gender' => 'female',
            'birth_date' => '2012-01-15',
            'relationship_to_guardian' => 'ابنة',
            'marital_status' => 'single',
            'is_primary_contact' => false,
        ]);

        $socialCase = SocialCase::create([
            'beneficiary_id' => $fileOwner->id,
            'case_number' => 'SC-2026-0001',
            'type' => 'financial',
            'status' => 'open',
            'priority' => 'high',
            'opened_at' => '2026-05-07',
            'summary' => 'يحتاج ملف المستفيد إلى دراسة دعم مالي.',
            'needs' => 'إيجار ومصاريف تعليم.',
            'monthly_income' => 2500,
            'monthly_expenses' => 4200,
        ]);

        $user = User::factory()->create();

        $note = SocialCaseNote::create([
            'social_case_id' => $socialCase->id,
            'user_id' => $user->id,
            'type' => 'visit',
            'note' => 'تمت زيارة ملف المستفيد وتوثيق الاحتياج.',
            'noted_at' => '2026-05-07 10:00:00',
        ]);

        $attachment = SocialCaseAttachment::create([
            'social_case_id' => $socialCase->id,
            'uploaded_by_id' => $user->id,
            'title' => 'صورة الهوية',
            'file_path' => 'social-cases/SC-2026-0001/id.pdf',
            'mime_type' => 'application/pdf',
            'size' => 2048,
        ]);

        $this->assertTrue($fileOwner->dependents->contains($dependent));
        $this->assertTrue($dependent->fileOwner->is($fileOwner));
        $this->assertTrue($fileOwner->socialCases->contains($socialCase));
        $this->assertTrue($socialCase->beneficiary->is($fileOwner));
        $this->assertTrue($socialCase->notes->contains($note));
        $this->assertTrue($socialCase->attachments->contains($attachment));
        $this->assertTrue($user->is($note->user));
        $this->assertTrue($user->is($attachment->uploadedBy));
        $this->assertSame('سارة أحمد الخالد', $dependent->full_name);
    }

    public function test_beneficiary_file_with_dependents_cannot_be_deleted(): void
    {
        $fileOwner = Beneficiary::create([
            'first_name' => 'خالد',
            'status' => Beneficiary::STATUS_ACTIVE,
        ]);

        Beneficiary::create([
            'file_owner_id' => $fileOwner->id,
            'first_name' => 'محمد',
        ]);

        $this->expectException(DomainException::class);

        $fileOwner->delete();
    }

    public function test_beneficiary_with_social_cases_cannot_be_deleted(): void
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'مستفيد',
            'status' => Beneficiary::STATUS_ACTIVE,
        ]);

        SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-0003',
            'summary' => 'حالة مرتبطة بالمستفيد.',
        ]);

        $this->expectException(DomainException::class);

        $beneficiary->delete();
    }

    public function test_social_case_with_notes_or_attachments_cannot_be_deleted(): void
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'مستفيد',
            'status' => Beneficiary::STATUS_ACTIVE,
        ]);

        $socialCase = SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-0004',
            'summary' => 'حالة لها ملاحظات.',
        ]);

        SocialCaseNote::create([
            'social_case_id' => $socialCase->id,
            'note' => 'ملاحظة مرتبطة.',
        ]);

        $this->expectException(DomainException::class);

        $socialCase->delete();
    }

    public function test_social_case_closing_sets_closed_at_and_reopening_clears_it(): void
    {
        $beneficiary = Beneficiary::create([
            'first_name' => 'مستفيد',
            'status' => Beneficiary::STATUS_ACTIVE,
        ]);

        $socialCase = SocialCase::create([
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-0006',
            'status' => SocialCase::STATUS_CLOSED,
            'summary' => 'حالة يتم إغلاقها.',
        ]);

        $this->assertTrue($socialCase->refresh()->isClosed());
        $this->assertNotNull($socialCase->closed_at);

        $socialCase->update(['status' => SocialCase::STATUS_UNDER_REVIEW]);

        $this->assertNull($socialCase->refresh()->closed_at);
    }
}
