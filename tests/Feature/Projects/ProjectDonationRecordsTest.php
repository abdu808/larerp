<?php

namespace Tests\Feature\Projects;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDonationRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_campaign_and_donation_records_can_be_created(): void
    {
        $project = Project::factory()->create([
            'title' => 'مشروع السلال الغذائية',
            'status' => 'active',
            'goal_amount' => 100000,
        ]);

        $campaign = Campaign::factory()->create([
            'project_id' => $project->id,
            'title' => 'حملة رمضان',
            'status' => 'active',
            'goal_amount' => 50000,
        ]);

        $donation = Donation::factory()
            ->forCampaign($campaign)
            ->create([
                'amount' => 250,
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'reference' => 'MANUAL-REF-001',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'مشروع السلال الغذائية',
            'status' => 'active',
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
            'payment_status' => 'paid',
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
}
