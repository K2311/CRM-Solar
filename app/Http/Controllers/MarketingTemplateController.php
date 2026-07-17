<?php

namespace App\Http\Controllers;

use App\Models\MarketingTemplate;
use Illuminate\Http\Request;

class MarketingTemplateController extends Controller
{
    public function index()
    {
        $templates = MarketingTemplate::latest()->paginate(20);
        return view('marketing.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('marketing.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'channel'  => 'required|in:sms,whatsapp,email,facebook,instagram',
            'subject'  => 'nullable|string|max:255',
            'body'     => 'required|string',
            'variables'=> 'nullable|array',
            'is_active'=> 'boolean',
        ]);

        MarketingTemplate::create($data);
        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function edit(MarketingTemplate $template)
    {
        return view('marketing.templates.edit', compact('template'));
    }

    public function update(Request $request, MarketingTemplate $template)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'channel'  => 'required|in:sms,whatsapp,email,facebook,instagram',
            'subject'  => 'nullable|string|max:255',
            'body'     => 'required|string',
            'variables'=> 'nullable|array',
            'is_active'=> 'boolean',
        ]);

        $template->update($data);
        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(MarketingTemplate $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
