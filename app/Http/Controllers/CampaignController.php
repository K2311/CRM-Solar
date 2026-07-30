<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\MarketingTemplate;
use App\Services\Marketing\CampaignDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(20);
        return view('marketing.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $templates = MarketingTemplate::where('is_active', true)->get();
        $customers = Customer::select('id', 'name', 'email', 'phone')->get();
        $leads     = Lead::select('id', 'title as name', 'email', 'phone')->get();
        return view('marketing.campaigns.create', compact('templates', 'customers', 'leads'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'channel'           => 'required|in:whatsapp,email',
            'subject'           => 'nullable|string|max:255',
            'body'              => 'required|string',
            'segment'           => 'required|in:all,customers_all,customers_active,leads_all,leads_active,leads_new,leads_engaged,leads_lost,manual',
            'selected_contacts' => 'nullable|array',
            'scheduled_at'      => 'nullable|date',
        ]);


        Campaign::create($data + ['status' => 'draft']);
        return redirect()->route('campaigns.index')->with('success', 'Campaign created.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('contacts');
        $stats = [
            'total'   => $campaign->total_contacts,
            'sent'    => $campaign->sent_count,
            'failed'  => $campaign->failed_count,
            'pending' => $campaign->contacts()->where('status', 'pending')->count(),
        ];
        return view('marketing.campaigns.show', compact('campaign', 'stats'));
    }

    public function edit(Campaign $campaign)
    {
        $templates = MarketingTemplate::where('is_active', true)->get();
        $customers = Customer::select('id', 'name', 'email', 'phone')->get();
        $leads     = Lead::select('id', 'title as name', 'email', 'phone')->get();
        return view('marketing.campaigns.edit', compact('campaign', 'templates', 'customers', 'leads'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'channel'           => 'required|in:whatsapp,email',
            'subject'           => 'nullable|string|max:255',
            'body'              => 'required|string',
            'segment'           => 'required|in:all,customers_all,customers_active,leads_all,leads_active,leads_new,leads_engaged,leads_lost,manual',
            'selected_contacts' => 'nullable|array',
            'scheduled_at'      => 'nullable|date',
        ]);


        $campaign->update($data);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->media_path) {
            Storage::disk('public')->delete($campaign->media_path);
        }
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function send(Campaign $campaign, CampaignDispatcher $dispatcher)
    {
        abort_if($campaign->status === 'sent', 400, 'Campaign already sent.');
        $dispatcher->dispatch($campaign);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign sent successfully!');
    }

    public function retry(Campaign $campaign, CampaignDispatcher $dispatcher)
    {
        abort_if($campaign->failed_count === 0, 400, 'No failed contacts to retry.');
        $dispatcher->retry($campaign);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Failed contacts have been retried!');
    }
}
