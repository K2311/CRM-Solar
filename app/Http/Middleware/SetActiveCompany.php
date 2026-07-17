<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $companyId = $user->company_id;

            // Super admin impersonation via session
            if ($user->is_super_admin && session()->has('impersonate_company_id')) {
                $companyId = session('impersonate_company_id');
            }

            if ($companyId) {
                app()->instance('current_company_id', $companyId);
                
                $company = \App\Models\Company::find($companyId);
                if ($company) {
                    // Set Global Timezone
                    config(['app.timezone' => $company->timezone ?? 'UTC']);
                    date_default_timezone_set($company->timezone ?? 'UTC');
                    
                    // Share Company with all views
                    view()->share('currentCompany', $company);
                }
            } elseif ($user->is_super_admin) {
                // Fallback for super-admin if no impersonation active
                $fallbackCompany = \App\Models\Company::first();
                if ($fallbackCompany) {
                    app()->instance('current_company_id', $fallbackCompany->id);
                    view()->share('currentCompany', $fallbackCompany);
                }
            }
        }

        return $next($request);
    }
}
