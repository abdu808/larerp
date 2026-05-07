<?php

namespace Tests\Feature\Projects;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProjectDonationRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_campaign_and_donation_records_can_be_created(): void
    {
        $project = Project::factory()->create([
            'title' => 'مشروع السلال الغذائية',
            'status' => Project::STATUS_ACTIVE,
            'goal_amount' => 100000,
        ]);

        $campaign = Campaign::factory()->create([
            'project_id' => $project->id,
            'title' => 'حملة رمضان',
            'status' => Campaign::STATUS_ACTIVE,
            'goal_amount' => 50000,
        ]);

        $donation = Donation::factory()
            ->forCampaign($campaign)
            ->create([
                'amount' => 250,
                'payment_status' => Donation::STATUS_PAID,
                'payment_method' => 'bank_transfer',
                'reference' => 'MANUAL-REF-001',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'مشروع السلال الغذائية',
            'status' => Project::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'project_id' => $project->id,
            'title' => 'حملة رمضان',
        ]);

        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'project_id' => $project->id,
            'campaign_id' => $campaign->id,
            'payment_status' => Donation::STATUS_PAID,
            'payment_method' => 'bank_transfer',
            'reference' => 'MANUAL-REF-001',
        ]);
    }

    public function test_project_campaign_and_donation_relationships_are_available(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()
            ->forCampaign($campaign)
            ->create();

        $project = $campaign->project()->firstOrFail();

        $this->assertTrue($project->campaigns->contains($campaign));
        $this->assertTrue($project->donations->contains($donation));
        $this->assertTrue($campaign->donations->contains($donation));
        $this->assertTrue($donation->project->is($project));
        $this->assertTrue($donation->campaign->is($campaign));
    }

    public function test_progress_percentage_is_calculated_for_projects_and_campaigns(): void
    {
        $project = Project::factory()->create([
            'goal_amount' => 1000,
            'collected_amount' => 375,
        ]);

        $campaign = Campaign::factory()->create([
            'goal_amount' => 2000,
            'collected_amount' => 1500,
        ]);

        $this->assertSame(38, $project->progress_percentage);
        $this->assertSame(75, $campaign->progress_percentage);
    }

    public function test_campaign_end_date_cannot_be_before_start_date(): void
    {
        $this->expectException(ValidationException::class);

        Campaign::factory()->create([
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDay(),
        ]);
    }

    public function test_project_collected_amount_cannot_exceed_goal_amount(): void
    {
        $this->expectException(ValidationException::class);

        Project::factory()->create([
            'goal_amount' => 100,
            'collected_amount' => 101,
        ]);
    }

    public function test_donation_amount_must_be_greater_than_zero(): void
    {
        $this->expectException(ValidationException::class);

        Donation::factory()->create([
            'amount' => 0,
        ]);
    }

    public function test_donation_campaign_must_belong_to_selected_project(): void
    {
        $campaign = Campaign::factory()->create();
        $otherProject = Project::factory()->create();

        $this->expectException(ValidationException::class);

        Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'project_id' => $otherProject->id,
        ]);
    }

    public function test_donation_inherits_project_from_campaign_when_project_is_empty(): void
    {
        $campaign = Campaign::factory()->create();

        $donation = Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'project_id' => null,
        ]);

        $this->assertTrue($donation->project->is($campaign->project));
    }
}
