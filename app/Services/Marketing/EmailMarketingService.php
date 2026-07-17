<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignMail;

class EmailMarketingService
{
    public function send(Company $company, string $to, string $subject, string $body): bool
    {
        try {
            // Use company-specific SMTP if configured
            $host     = $company->setting('mail_host');
            $port     = $company->setting('mail_port');
            $username = $company->setting('mail_username');
            $password = $company->setting('mail_password');
            $fromAddr = $company->setting('mail_from_address', config('mail.from.address'));
            $fromName = $company->setting('mail_from_name', $company->name);

            if ($host && $username) {
                config([
                    'mail.mailers.smtp.host'       => $host,
                    'mail.mailers.smtp.port'       => $port,
                    'mail.mailers.smtp.username'   => $username,
                    'mail.mailers.smtp.password'   => $password,
                    'mail.mailers.smtp.encryption' => $company->setting('mail_encryption', 'tls'),
                    'mail.from.address'            => $fromAddr,
                    'mail.from.name'               => $fromName,
                ]);
            }

            Mail::to($to)->send(new CampaignMail($subject, $body, $fromName));
            return true;
        } catch (\Exception $e) {
            Log::error("Email send failed: " . $e->getMessage());
            return false;
        }
    }
}
