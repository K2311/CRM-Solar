<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function companies()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $companies = Company::withCount('users')->latest()->paginate(20);
        return view('admin.companies.index', compact('companies'));
    }

    public function impersonate(Company $company)
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        session(['impersonate_company_id' => $company->id]);
        return redirect()->route('dashboard')->with('success', "Now managing {$company->name}");
    }

    public function stopImpersonating()
    {
        session()->forget('impersonate_company_id');
        return redirect()->route('admin.companies')->with('success', "Returned to super-admin view.");
    }
}
