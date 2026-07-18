<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Services\Marketing\CampaignDispatcher;

class PublishScheduledCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch scheduled marketing campaigns';

    /**
     * Execute the console command.
     */
    public function handle(CampaignDispatcher $dispatcher)
    {
        $allCampaigns = Campaign::with('company')
            ->where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->get();

        $campaigns = $allCampaigns->filter(function ($campaign) {
            $companyTimezone = $campaign->company->timezone ?? 'UTC';
            $companyNowStr = now()->timezone($companyTimezone)->format('Y-m-d H:i:s');
            // Compare the database string (local time of company) against the company's current local time
            return $campaign->scheduled_at->format('Y-m-d H:i:s') <= $companyNowStr;
        });

        $count = $campaigns->count();

        foreach ($campaigns as $campaign) {
            try {
                $dispatcher->dispatch($campaign);
                $this->info("Dispatched campaign: {$campaign->id}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch campaign {$campaign->id}: " . $e->getMessage());
                // Set status back to draft or failed so we know it didn't succeed,
                // but CampaignDispatcher handles the internal contacts.
            }
        }

        $this->info("Processed {$count} scheduled campaigns.");
    }
}
