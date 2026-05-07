<?php

namespace Tests\Feature\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\Family;
use App\Models\SocialCase;
use App\Models\SocialCaseAttachment;
use App\Models\SocialCaseNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialCaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_beneficiary_and_social_case_relationships_can_be_created(): void
    {
        $family = Family::create([
            'code' => 'FAM-1001',
            'name' => 'أسرة أحمد',
            'guardian_name' => 'أحمد محمد',
            'phone' => '0500000000',
            'city' => 'الرياض',
            'district' => 'النسيم',
            'status' => 'active',
            'registered_at' => '2026-05-07',
        ]);

        $beneficiary = Beneficiary::create([
            'family_id' => $family->id,
            'national_id' => '1000000001',
            'first_name' => 'سارة',
            'father_name' => 'أحمد',
            'family_name' => 'محمد',
            'gender' => 'female',
            'birth_date' => '2012-01-15',
            'relationship_to_guardian' => 'ابنة',
            'marital_status' => 'single',
            'is_primary_contact' => false,
        ]);

        $socialCase = SocialCase::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-0001',
            'type' => 'financial',
            'status' => 'open',
            'priority' => 'high',
            'opened_at' => '2026-05-07',
            'summary' => 'تحتاج الأسرة إلى دراسة دعم مالي.',
            'needs' => 'إيجار ومصاريف تعليم.',
            'monthly_income' => 2500,
            'monthly_expenses' => 4200,
        ]);

        $user = User::factory()->create();

        $note = SocialCaseNote::create([
            'social_case_id' => $socialCase->id,
            'user_id' => $user->id,
            'type' => 'visit',
            'note' => 'تمت زيارة الأسرة وتوثيق الاحتياج.',
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

        $this->assertTrue($family->beneficiaries->contains($beneficiary));
        $this->assertTrue($family->socialCases->contains($socialCase));
        $this->assertTrue($beneficiary->socialCases->contains($socialCase));
        $this->assertTrue($socialCase->notes->contains($note));
        $this->assertTrue($socialCase->attachments->contains($attachment));
        $this->assertTrue($user->is($note->user));
        $this->assertTrue($user->is($attachment->uploadedBy));
        $this->assertSame('سارة أحمد محمد', $beneficiary->full_name);
    }

    public function test_deleting_family_removes_its_beneficiaries_cases_notes_and_attachments(): void
    {
        $family = Family::create([
            'code' => 'FAM-1002',
            'name' => 'أسرة خالد',
            'guardian_name' => 'خالد عبدالله',
        ]);

        $beneficiary = Beneficiary::create([
            'family_id' => $family->id,
            'first_name' => 'محمد',
        ]);

        $socialCase = SocialCase::create([
            'family_id' => $family->id,
            'beneficiary_id' => $beneficiary->id,
            'case_number' => 'SC-2026-0002',
            'summary' => 'حالة اختبارية.',
        ]);

        SocialCaseNote::create([
            'social_case_id' => $socialCase->id,
            'note' => 'ملاحظة اختبارية.',
        ]);

        SocialCaseAttachment::create([
            'social_case_id' => $socialCase->id,
            'title' => 'مرفق اختباري',
            'file_path' => 'social-cases/SC-2026-0002/test.pdf',
        ]);

        $family->delete();

        $this->assertDatabaseEmpty('families');
        $this->assertDatabaseEmpty('beneficiaries');
        $this->assertDatabaseEmpty('social_cases');
        $this->assertDatabaseEmpty('social_case_notes');
        $this->assertDatabaseEmpty('social_case_attachments');
    }
}
