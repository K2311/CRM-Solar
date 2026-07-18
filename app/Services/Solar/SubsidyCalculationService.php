<?php

namespace App\Services\Solar;

use App\Models\Company;

class SubsidyCalculationService
{
    /**
     * Calculate central and state subsidies based on PM Surya Ghar guidelines.
     */
    public function calculate(Company $company, float $kw): array
    {
        if ($kw <= 0) {
            return [
                'central' => 0.0,
                'state' => 0.0,
                'total' => 0.0,
            ];
        }

        // 1. Central Subsidy Calculation
        // Capped at 3kW (max ₹78,000)
        // Up to 2 kW: ₹30,000 / kW
        // Additional kW up to 3 kW: ₹18,000 / kW
        if ($kw <= 2) {
            $central = $kw * 30000.0;
        } else {
            $base = 60000.0;
            $extra = min(1.0, $kw - 2.0) * 18000.0;
            $central = $base + $extra;
        }

        // 2. State Subsidy Calculation
        $stateType = $company->setting('state_subsidy_type', 'flat'); // flat or per_kw
        $stateRate = floatval($company->setting('state_subsidy_rate', 0));

        if ($stateType === 'per_kw') {
            $state = $kw * $stateRate;
        } else {
            // Flat state subsidy
            $state = $stateRate;
        }

        return [
            'central' => round($central, 2),
            'state' => round($state, 2),
            'total' => round($central + $state, 2),
        ];
    }
}
