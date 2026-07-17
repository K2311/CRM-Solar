<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Traits\HasTenant;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use HasTenant;

    public function index()
    {
        $company = $this->tenantRequired();
        $settings = $company->settings->pluck('value', 'key');
        return view('settings.index', compact('company', 'settings'));
    }

    public function update(Request $request)
    {
        $company = $this->tenantRequired();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'timezone' => 'required|string',
            'currency' => 'required|string',
            'settings' => 'nullable|array',
        ]);

        $company->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'timezone' => $data['timezone'],
            'currency' => $data['currency'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $company->update(['logo' => $path]);
        }

        if (isset($data['settings'])) {
            foreach ($data['settings'] as $key => $value) {
                CompanySetting::updateOrCreate(
                    ['company_id' => $company->id, 'key' => $key],
                    ['value' => $value]
                );
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
