<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Installation;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (\App\Models\Company::all() as $company) {
            $staff = User::where('company_id', $company->id)->where('role', 'member')->first();
            
            for ($i = 1; $i <= 5; $i++) {
                $customer = Customer::create([
                    'company_id' => $company->id,
                    'name' => "Solar Client {$i} - {$company->name}",
                    'email' => "client{$i}@" . \Str::slug($company->name) . ".com",
                    'phone' => '123456789' . $i,
                    'status' => 'active',
                ]);

                $lead = Lead::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'assigned_user_id' => $staff?->id,
                    'title' => "Rooftop Install Project",
                    'stage' => 'won',
                    'value' => 8000 + ($i * 1000),
                ]);

                $quote = Quote::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'lead_id' => $lead->id,
                    'quote_number' => Quote::generateNumber($company->id),
                    'status' => 'accepted',
                    'total' => $lead->value,
                ]);

                Installation::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'lead_id' => $lead->id,
                    'quote_id' => $quote->id,
                    'status' => 'completed',
                    'scheduled_date' => now()->subDays(10),
                    'completed_date' => now()->subDays(1),
                ]);
            }
        }
    }
}
