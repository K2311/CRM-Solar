<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\MarketingTemplate;
use App\Services\Marketing\CampaignDispatcher;
use Illuminate\Http\Request;

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
        return view('marketing.campaigns.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'channel'      => 'required|in:sms,whatsapp,email,facebook,instagram',
            'subject'      => 'nullable|string|max:255',
            'body'         => 'required|string',
            'segment'      => 'required|in:all,leads,customers',
            'scheduled_at' => 'nullable|date',
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
        return view('marketing.campaigns.edit', compact('campaign', 'templates'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'channel'      => 'required|in:sms,whatsapp,email,facebook,instagram',
            'subject'      => 'nullable|string|max:255',
            'body'         => 'required|string',
            'segment'      => 'required|in:all,leads,customers',
            'scheduled_at' => 'nullable|date',
        ]);
        $campaign->update($data);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function send(Campaign $campaign, CampaignDispatcher $dispatcher)
    {
        abort_if($campaign->status === 'sent', 400, 'Campaign already sent.');
        $dispatcher->dispatch($campaign);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign sent successfully!');
    }
}
