<?php

namespace App\Traits;

use App\Models\Company;

trait HasTenant
{
    /**
     * Get the current active company (tenant).
     */
    protected function currentCompany(): ?Company
    {
        $companyId = $this->currentCompanyId();
        
        if (!$companyId) {
            return null;
        }

        return Company::find($companyId);
    }

    /**
     * Get the current active company ID.
     */
    protected function currentCompanyId(): ?int
    {
        if (app()->bound('current_company_id')) {
            return app('current_company_id');
        }

        return auth()->check() ? auth()->user()->company_id : null;
    }

    /**
     * Abort if no company is active.
     */
    protected function tenantRequired(): Company
    {
        $company = $this->currentCompany();
        
        if (!$company) {
            if (request()->expectsJson()) {
                abort(403, 'No active company context.');
            }
            
            if (auth()->check() && auth()->user()->is_super_admin) {
                abort(redirect()->route('admin.companies')->with('info', 'Please select a company to manage.'));
            }
            
            abort(403, 'Active company context required. Your account may not be correctly associated with a company.');
        }

        return $company;
    }
}
