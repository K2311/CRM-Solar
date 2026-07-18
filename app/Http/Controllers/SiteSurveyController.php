<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\SiteSurvey;
use App\Models\User;
use Illuminate\Http\Request;

class SiteSurveyController extends Controller
{
    use \App\Traits\HasTenant;

    public function index(Request $request)
    {
        $query = SiteSurvey::with('lead.customer', 'technician');
        $surveys = $query->latest()->paginate(20);
        return view('site_surveys.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        $company = $this->tenantRequired();
        $lead = null;
        if ($request->lead_id) {
            $lead = Lead::with('customer')->findOrFail($request->lead_id);
        }

        $leads = Lead::with('customer')->where('company_id', $company->id)->get();
        $technicians = User::where('company_id', $company->id)->get();

        return view('site_surveys.create', compact('lead', 'leads', 'technicians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_id'            => 'required|exists:leads,id',
            'technician_id'      => 'required|exists:users,id',
            'roof_area_sqft'     => 'required|numeric|min:1',
            'roof_type'          => 'required|string|max:50',
            'shading_details'    => 'required|string|max:50',
            'discom_name'        => 'required|string|max:100',
            'sanctioned_load_kw' => 'required|numeric|min:0.1',
            'consumer_number'    => 'required|string|max:100',
            'notes'              => 'nullable|string',
            'survey_date'        => 'required|date',
            'photos.*'           => 'nullable|image|max:5000', // Allow multiple images up to 5MB each
        ]);

        $lead = Lead::findOrFail($data['lead_id']);
        $company = $this->tenantRequired();

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photoPaths[] = $file->store('surveys', 'public');
            }
        }

        $survey = SiteSurvey::create([
            'company_id'         => $company->id,
            'lead_id'            => $lead->id,
            'customer_id'        => $lead->customer_id,
            'technician_id'      => $data['technician_id'],
            'roof_area_sqft'     => $data['roof_area_sqft'],
            'roof_type'          => $data['roof_type'],
            'shading_details'    => $data['shading_details'],
            'discom_name'        => $data['discom_name'],
            'sanctioned_load_kw' => $data['sanctioned_load_kw'],
            'consumer_number'    => $data['consumer_number'],
            'notes'              => $data['notes'] ?? null,
            'survey_date'        => $data['survey_date'],
            'photos'             => $photoPaths,
        ]);

        // Auto move lead to 'survey_scheduled' or update if already done
        if ($lead->stage === 'new' || $lead->stage === 'contacted') {
            $lead->update(['stage' => 'survey_scheduled']);
        }

        return redirect()->route('leads.show', $lead->id)->with('success', 'Site survey details captured successfully.');
    }

    public function show(SiteSurvey $siteSurvey)
    {
        $siteSurvey->load('lead.customer', 'customer', 'technician');
        return view('site_surveys.show', compact('siteSurvey'));
    }

    public function edit(SiteSurvey $siteSurvey)
    {
        $company = $this->tenantRequired();
        $technicians = User::where('company_id', $company->id)->get();
        return view('site_surveys.edit', compact('siteSurvey', 'technicians'));
    }

    public function update(Request $request, SiteSurvey $siteSurvey)
    {
        $data = $request->validate([
            'technician_id'      => 'required|exists:users,id',
            'roof_area_sqft'     => 'required|numeric|min:1',
            'roof_type'          => 'required|string|max:50',
            'shading_details'    => 'required|string|max:50',
            'discom_name'        => 'required|string|max:100',
            'sanctioned_load_kw' => 'required|numeric|min:0.1',
            'consumer_number'    => 'required|string|max:100',
            'notes'              => 'nullable|string',
            'survey_date'        => 'required|date',
            'photos.*'           => 'nullable|image|max:5000',
        ]);

        $photoPaths = $siteSurvey->photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photoPaths[] = $file->store('surveys', 'public');
            }
        }

        $siteSurvey->update([
            'technician_id'      => $data['technician_id'],
            'roof_area_sqft'     => $data['roof_area_sqft'],
            'roof_type'          => $data['roof_type'],
            'shading_details'    => $data['shading_details'],
            'discom_name'        => $data['discom_name'],
            'sanctioned_load_kw' => $data['sanctioned_load_kw'],
            'consumer_number'    => $data['consumer_number'],
            'notes'              => $data['notes'] ?? null,
            'survey_date'        => $data['survey_date'],
            'photos'             => $photoPaths,
        ]);

        return redirect()->route('leads.show', $siteSurvey->lead_id)->with('success', 'Site survey updated.');
    }

    public function destroy(SiteSurvey $siteSurvey)
    {
        $leadId = $siteSurvey->lead_id;
        $siteSurvey->delete();
        return redirect()->route('leads.show', $leadId)->with('success', 'Site survey removed.');
    }
}
