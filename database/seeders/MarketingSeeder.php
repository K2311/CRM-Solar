<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\MarketingTemplate;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (\App\Models\Company::all() as $company) {
            MarketingTemplate::create([
                'company_id' => $company->id,
                'name' => 'Solar Promo Email',
                'channel' => 'email',
                'subject' => 'Go Solar and Save!',
                'body' => '<h1>Exclusive Offer</h1><p>Switch to solar today and save 30% on your energy bills.</p>',
            ]);

            Campaign::create([
                'company_id' => $company->id,
                'name' => 'Summer Launch 2024',
                'channel' => 'email',
                'status' => 'draft',
                'subject' => 'Summer Solar Savings',
                'body' => 'Get ready for the sun with our latest deals.',
                'segment' => 'all',
            ]);
        }
    }
}
