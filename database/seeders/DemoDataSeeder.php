<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\GstInvoice;
use App\Models\GstInvoiceItem;
use App\Models\Installation;
use App\Models\InstallationMilestone;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\ServiceTicket;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_IN'); // Using Indian locale for realistic names/addresses
        $companies = Company::all();

        // Constants for generation
        $customersPerCompany = 350; // Massively increased to ensure plenty of quotes for pagination testing

        $panelBrands = ['Waaree', 'Vikram Solar', 'Tata Power Solar', 'Adani Solar', 'Trina Solar'];
        $inverterBrands = ['Growatt', 'SolarEdge', 'Fronius', 'Enphase', 'Luminous'];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($companies as $company) {
                $this->command->info("Seeding robust demo data for company: {$company->name}");

                $staff = User::where('company_id', $company->id)->whereIn('role', ['member', 'sales', 'technician'])->get();
                $salesRep = $staff->where('role', 'sales')->first() ?? $staff->first();
                $technician = $staff->where('role', 'technician')->first() ?? $staff->first();

                for ($i = 0; $i < $customersPerCompany; $i++) {
                    // Generate a random date within the last 3 years (1095 days)
                    $createdAt = Carbon::now()->subDays(rand(1, 1095));

                    // 1. Create Customer
                    $customer = Customer::create([
                        'company_id' => $company->id,
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'phone' => '9' . $faker->numerify('#########'),
                        'address' => $faker->streetAddress,
                        'city' => $faker->city,
                        'state' => 'Maharashtra',
                        'zip' => $faker->postcode,
                        'status' => 'active',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // 2. Create Lead
                    // Weight towards won and active pipeline stages
                    $isRecent = $createdAt->diffInDays(Carbon::now()) < 60;
                    
                    if ($isRecent) {
                        $stage = $faker->randomElement(['new', 'contacted', 'qualified', 'proposal', 'won']);
                    } else {
                        $stage = $faker->randomElement(['won', 'won', 'won', 'lost']); // Historically, mostly closed won or lost
                    }

                    $systemSize = $faker->randomElement([3, 5, 8, 10, 15, 20]);
                    $leadValue = $systemSize * 60000; // Roughly 60k per kW

                    $lead = Lead::create([
                        'company_id' => $company->id,
                        'customer_id' => $customer->id,
                        'assigned_user_id' => $salesRep?->id,
                        'title' => $systemSize . "kW Residential Rooftop Solar",
                        'stage' => $stage,
                        'value' => $leadValue,
                        'created_at' => $createdAt,
                        'updated_at' => clone $createdAt->addDays(rand(1, 10)),
                    ]);

                    // 3. Create Quote (if beyond qualified)
                    $quote = null;
                    if (in_array($stage, ['proposal', 'won', 'lost'])) {
                        $quoteDate = clone $createdAt->addDays(rand(2, 5));
                        $quote = Quote::create([
                            'company_id' => $company->id,
                            'customer_id' => $customer->id,
                            'lead_id' => $lead->id,
                            'quote_number' => Quote::generateNumber($company->id),
                            'status' => $stage === 'won' ? 'accepted' : ($stage === 'lost' ? 'rejected' : 'sent'),
                            'total' => $leadValue,
                            'valid_until' => clone $quoteDate->addDays(30),
                            'created_at' => $quoteDate,
                            'updated_at' => clone $quoteDate->addDays(rand(1, 5)),
                        ]);

                        QuoteItem::create([
                            'quote_id' => $quote->id,
                            'description' => $systemSize . 'kW Solar Panel System',
                            'qty' => 1,
                            'unit_price' => $leadValue * 0.7,
                            'subtotal' => $leadValue * 0.7,
                        ]);
                        QuoteItem::create([
                            'quote_id' => $quote->id,
                            'description' => 'Installation & Commissioning',
                            'qty' => 1,
                            'unit_price' => $leadValue * 0.3,
                            'subtotal' => $leadValue * 0.3,
                        ]);
                    }

                    // 4. Create Installation, Invoices, and Payments (if won)
                    if ($stage === 'won') {
                        $installDate = clone $createdAt->addDays(rand(10, 30));
                        $isCompleted = $installDate->isPast();

                        $installation = Installation::create([
                            'company_id' => $company->id,
                            'customer_id' => $customer->id,
                            'lead_id' => $lead->id,
                            'quote_id' => $quote->id,
                            'status' => $isCompleted ? 'completed' : 'scheduled',
                            'scheduled_date' => $installDate,
                            'completed_date' => $isCompleted ? clone $installDate->addDays(rand(3, 7)) : null,
                            'panel_brand' => $faker->randomElement($panelBrands),
                            'panel_count' => $systemSize * 2, // roughly 2 panels per kw
                            'inverter_brand' => $faker->randomElement($inverterBrands),
                            'system_size_kw' => $systemSize,
                            'assigned_user_id' => $technician?->id,
                            'created_at' => clone $createdAt->addDays(7),
                            'updated_at' => $isCompleted ? clone $installDate->addDays(7) : Carbon::now(),
                        ]);

                        // Generate Milestones
                        $milestones = InstallationMilestone::defaultMilestones();
                        foreach ($milestones as $num => $name) {
                            InstallationMilestone::create([
                                'installation_id' => $installation->id,
                                'milestone_number' => $num,
                                'name' => $name,
                                'status' => 'pending',
                            ]);
                        }

                        if ($isCompleted) {
                            InstallationMilestone::where('installation_id', $installation->id)->update([
                                'status' => 'completed',
                                'completed_at' => clone $installDate->addDays(rand(1, 5))
                            ]);
                        } elseif ($installDate->isPast() === false && $installDate->diffInDays(Carbon::now()) < 5) {
                            // In progress
                            InstallationMilestone::where('installation_id', $installation->id)->limit(3)->update([
                                'status' => 'completed',
                                'completed_at' => Carbon::now()->subDays(1)
                            ]);
                            $installation->update(['status' => 'in_progress']);
                        }

                        // Invoicing
                        if ($isCompleted || rand(1, 100) > 50) { // If completed or just random advance invoice
                            $subtotal = $leadValue;
                            $cgstAmount = $subtotal * 0.06;
                            $sgstAmount = $subtotal * 0.06;
                            $totalGst = $cgstAmount + $sgstAmount;
                            $total = $subtotal + $totalGst;

                            $invoiceDate = clone $createdAt->addDays(8);
                            
                            // Payment status probability
                            $invoiceStatus = 'paid';
                            if ($invoiceDate->diffInDays(Carbon::now()) < 30) {
                                $invoiceStatus = $faker->randomElement(['paid', 'unpaid']);
                            }

                            $invoice = GstInvoice::create([
                                'company_id' => $company->id,
                                'customer_id' => $customer->id,
                                'installation_id' => $installation->id,
                                'invoice_number' => GstInvoice::generateNumber($company->id),
                                'status' => $invoiceStatus,
                                'invoice_date' => $invoiceDate,
                                'subtotal' => $subtotal,
                                'taxable_value' => $subtotal,
                                'cgst_rate' => 6,
                                'cgst_amount' => $cgstAmount,
                                'sgst_rate' => 6,
                                'sgst_amount' => $sgstAmount,
                                'total_gst' => $totalGst,
                                'grand_total' => $total,
                                'created_at' => $invoiceDate,
                                'updated_at' => $invoiceDate,
                            ]);

                            // Payments
                            if ($invoiceStatus === 'paid') {
                                $payAmount = $total;
                                $payDate = clone $invoiceDate->addDays(rand(1, 14));
                                
                                Payment::create([
                                    'company_id' => $company->id,
                                    'quote_id' => $quote->id,
                                    'customer_id' => $customer->id,
                                    'amount' => $payAmount,
                                    'method' => $faker->randomElement(['bank_transfer', 'cash', 'online']),
                                    'reference' => strtoupper(Str::random(10)),
                                    'payment_date' => $payDate > Carbon::now() ? Carbon::now() : $payDate,
                                    'created_at' => $payDate > Carbon::now() ? Carbon::now() : $payDate,
                                ]);
                            }
                        }
                    }

                    // 5. Service Tickets
                    // ~20% of customers have a ticket
                    if (rand(1, 100) <= 20) {
                        $ticketDate = clone $createdAt->addDays(rand(30, 200));
                        if ($ticketDate > Carbon::now()) {
                            $ticketDate = Carbon::now()->subDays(rand(1, 10));
                        }
                        $isTicketOpen = $ticketDate->diffInDays(Carbon::now()) < 15;
                        
                        ServiceTicket::create([
                            'company_id' => $company->id,
                            'customer_id' => $customer->id,
                            'title' => $faker->randomElement(['Inverter showing error code', 'System performance drop', 'Panel cleaning request', 'Wifi module offline', 'Wiring issue']),
                            'description' => $faker->paragraph,
                            'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                            'status' => $isTicketOpen ? $faker->randomElement(['open', 'in_progress']) : 'resolved',
                            'created_at' => $ticketDate,
                            'updated_at' => clone $ticketDate->addDays(rand(1, 5)),
                        ]);
                    }
                }
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $e;
        }
    }
}
