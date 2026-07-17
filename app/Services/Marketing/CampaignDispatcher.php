<?php

namespace App\Services\Marketing;

use App\Models\Campaign;
use App\Models\CampaignContact;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;

class CampaignDispatcher
{
    public function __construct(
        private SmsService           $sms,
        private WhatsAppService      $whatsapp,
        private EmailMarketingService $email,
        private FacebookService      $facebook,
        private InstagramService     $instagram,
    ) {}

    public function dispatch(Campaign $campaign): void
    {
        $company = $campaign->company;
        $campaign->update(['status' => 'sending', 'sent_at' => now()]);

        // Build contacts from segment
        $contacts = $this->buildContacts($campaign, $company);
        $campaign->update(['total_contacts' => $contacts->count()]);

        foreach ($contacts as $contact) {
            $cc = CampaignContact::create([
                'campaign_id'  => $campaign->id,
                'contact_type' => get_class($contact) === Customer::class ? 'customer' : 'lead',
                'contact_id'   => $contact->id,
                'name'         => $contact->name ?? ($contact->customer->name ?? ''),
                'phone'        => $contact->phone ?? ($contact->customer->phone ?? ''),
                'email'        => $contact->email ?? ($contact->customer->email ?? ''),
                'status'       => 'pending',
            ]);

            $this->sendToContact($campaign, $company, $cc);
        }

        $sent   = $campaign->contacts()->where('status', 'sent')->count();
        $failed = $campaign->contacts()->where('status', 'failed')->count();
        $campaign->update(['status' => 'sent', 'sent_count' => $sent, 'failed_count' => $failed]);
    }

    private function buildContacts(Campaign $campaign, Company $company)
    {
        return match($campaign->segment) {
            'customers' => Customer::where('company_id', $company->id)->get(),
            'leads'     => Lead::where('company_id', $company->id)
                              ->whereNotIn('stage', ['won', 'lost'])->get(),
            default     => Customer::where('company_id', $company->id)->get(),
        };
    }

    private function sendToContact(Campaign $campaign, Company $company, CampaignContact $cc): void
    {
        $body    = $campaign->body;
        $subject = $campaign->subject ?? $campaign->name;
        $success = false;

        $success = match($campaign->channel) {
            'sms'       => $cc->phone ? $this->sms->send($company, $cc->phone, $body) : false,
            'whatsapp'  => $cc->phone ? $this->whatsapp->send($company, $cc->phone, $body) : false,
            'email'     => $cc->email ? $this->email->send($company, $cc->email, $subject, $body) : false,
            'facebook'  => $this->facebook->post($company, $body),
            'instagram' => $this->instagram->post($company, $body),
            default     => false,
        };

        $cc->update([
            'status'  => $success ? 'sent' : 'failed',
            'sent_at' => now(),
        ]);
    }
}
