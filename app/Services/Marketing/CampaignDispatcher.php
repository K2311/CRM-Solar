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
        private WhatsAppService      $whatsapp,
        private EmailMarketingService $email,
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

    public function retry(Campaign $campaign): void
    {
        $company = $campaign->company;
        $campaign->update(['status' => 'sending']);

        $failedContacts = $campaign->contacts()->where('status', 'failed')->get();

        foreach ($failedContacts as $cc) {
            $this->sendToContact($campaign, $company, $cc);
        }

        $sent   = $campaign->contacts()->where('status', 'sent')->count();
        $failed = $campaign->contacts()->where('status', 'failed')->count();
        $campaign->update(['status' => 'sent', 'sent_count' => $sent, 'failed_count' => $failed]);
    }

    private function buildContacts(Campaign $campaign, Company $company)
    {
        if ($campaign->segment === 'manual' && is_array($campaign->selected_contacts)) {
            $customerIds = [];
            $leadIds = [];
            foreach ($campaign->selected_contacts as $sc) {
                if (str_starts_with($sc, 'customer_')) $customerIds[] = str_replace('customer_', '', $sc);
                if (str_starts_with($sc, 'lead_')) $leadIds[] = str_replace('lead_', '', $sc);
            }
            
            $customers = Customer::where('company_id', $company->id)->whereIn('id', $customerIds)->get();
            $leads = Lead::where('company_id', $company->id)->whereIn('id', $leadIds)->get();
            return $customers->merge($leads);
        }

        return match($campaign->segment) {
            'all'              => Customer::where('company_id', $company->id)->get()
                                    ->merge(Lead::where('company_id', $company->id)->get()),
            'customers_all'    => Customer::where('company_id', $company->id)->get(),
            'customers_active' => Customer::where('company_id', $company->id)->where('status', 'active')->get(),
            
            'leads_all'        => Lead::where('company_id', $company->id)->get(),
            'leads_active'     => Lead::where('company_id', $company->id)->whereNotIn('stage', ['won', 'lost', 'junk'])->get(),
            'leads_new'        => Lead::where('company_id', $company->id)->where('stage', 'new')->get(),
            'leads_engaged'    => Lead::where('company_id', $company->id)->whereIn('stage', ['contacted', 'survey_scheduled', 'quote_sent', 'negotiation'])->get(),
            'leads_lost'       => Lead::where('company_id', $company->id)->where('stage', 'lost')->get(),
            
            default            => Customer::where('company_id', $company->id)->get(), // Fallback
        };
    }

    private function sendToContact(Campaign $campaign, Company $company, CampaignContact $cc): void
    {
        $body = str_replace(
            ['{name}', '{company}', '{email}', '{phone}'],
            [$cc->name, $company->name, $cc->email, $cc->phone],
            $campaign->body
        );

        $subject = $campaign->subject ?? $campaign->name;
        $subject = str_replace(
            ['{name}', '{company}', '{email}', '{phone}'],
            [$cc->name, $company->name, $cc->email, $cc->phone],
            $subject
        );

        $success = false;

        $success = match($campaign->channel) {
            'whatsapp'  => $cc->phone ? $this->whatsapp->send($company, $cc->phone, $body) : false,
            'email'     => $cc->email ? $this->email->send($company, $cc->email, $subject, $body) : false,
            default     => false,
        };

        $cc->update([
            'status'  => $success ? 'sent' : 'failed',
            'sent_at' => now(),
        ]);
    }
}
