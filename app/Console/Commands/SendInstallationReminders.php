<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Installation;
use App\Services\Marketing\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendInstallationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installations:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders to technicians for upcoming installations.';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        $this->info('Starting installation reminders job...');

        // 1. Reminders for Tomorrow
        $tomorrow = Carbon::tomorrow()->toDateString();
        $tomorrowInstallations = Installation::with(['assignedUser', 'customer', 'company'])
            ->where('status', 'scheduled')
            ->whereDate('scheduled_date', $tomorrow)
            ->whereNotNull('assigned_user_id')
            ->get();

        foreach ($tomorrowInstallations as $install) {
            if ($install->assignedUser && $install->assignedUser->phone) {
                $body = "Hi {$install->assignedUser->name},\n\nThis is a reminder that you have an installation scheduled for TOMORROW ({$install->scheduled_date->format('M d, Y')}).\n\nCustomer: {$install->customer->first_name} {$install->customer->last_name}\nSystem Size: {$install->system_size_kw} kW\n\nPlease ensure you have all necessary materials prepared.";
                
                try {
                    $whatsAppService->send($install->company, $install->assignedUser->phone, $body);
                    $this->info("Reminder (Tomorrow) sent to {$install->assignedUser->name} for Installation #{$install->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send WhatsApp reminder for Installation {$install->id}: " . $e->getMessage());
                    $this->error("Failed to send to {$install->assignedUser->name}");
                }
            }
        }

        // 2. Reminders for Today
        $today = Carbon::today()->toDateString();
        $todayInstallations = Installation::with(['assignedUser', 'customer', 'company'])
            ->where('status', 'scheduled')
            ->whereDate('scheduled_date', $today)
            ->whereNotNull('assigned_user_id')
            ->get();

        foreach ($todayInstallations as $install) {
            if ($install->assignedUser && $install->assignedUser->phone) {
                $body = "Hi {$install->assignedUser->name},\n\nReminder: You have an installation scheduled for TODAY!\n\nCustomer: {$install->customer->first_name} {$install->customer->last_name}\nSystem Size: {$install->system_size_kw} kW\n\nPlease arrive on time and update the milestone progress in the CRM.\n\nGood luck!";
                
                try {
                    $whatsAppService->send($install->company, $install->assignedUser->phone, $body);
                    $this->info("Reminder (Today) sent to {$install->assignedUser->name} for Installation #{$install->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send WhatsApp reminder for Installation {$install->id}: " . $e->getMessage());
                    $this->error("Failed to send to {$install->assignedUser->name}");
                }
            }
        }

        $this->info('Completed installation reminders job.');
    }
}
