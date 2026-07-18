<?php

namespace App\Console\Commands;

use App\Models\AmcContract;
use App\Services\Marketing\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckAmcExpiry extends Command
{
    protected $signature = 'amc:check-expiry';
    protected $description = 'Check AMC contracts expiring in 30 days and send automated WhatsApp/Email reminders';

    public function handle(WhatsAppService $whatsapp)
    {
        $targetDate = now()->addDays(30)->toDateString();
        
        $contracts = AmcContract::where('expiry_date', $targetDate)
            ->where('status', 'active')
            ->get();

        $this->info("Found " . $contracts->count() . " active AMC contracts expiring on {$targetDate}.");

        foreach ($contracts as $contract) {
            $company = $contract->company;
            $customer = $contract->customer;

            // Check if reminder is enabled
            if ($company->setting('notify_amc_renewal') !== '1') {
                continue;
            }

            $message = "Hello {$customer->name}, your solar Annual Maintenance Contract (AMC) is expiring in 30 days on {$contract->expiry_date->format('M d, Y')}. Please contact us to renew.";

            // 1. Send WhatsApp via Meta Cloud API
            if ($customer->phone) {
                $whatsapp->send($company, $customer->phone, $message);
            }

            // 2. Send Email
            if ($customer->email) {
                try {
                    Mail::raw($message, function ($mail) use ($customer, $company) {
                        $mail->to($customer->email)
                            ->subject("AMC Expiry Reminder - " . $company->name)
                            ->from($company->email ?? 'no-reply@solartech.com', $company->name);
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to send AMC expiry email to {$customer->email}: " . $e->getMessage());
                }
            }

            // Record client activity
            \App\Models\Activity::create([
                'company_id'   => $company->id,
                'subject_type' => AmcContract::class,
                'subject_id'   => $contract->id,
                'type'         => 'amc_reminder',
                'description'  => "Sent 30-day AMC renewal reminder to {$customer->name}.",
            ]);

            $this->info("Reminder sent to {$customer->name} (AMC #{$contract->id})");
        }

        return Command::SUCCESS;
    }
}
