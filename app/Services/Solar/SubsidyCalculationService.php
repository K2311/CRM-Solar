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
        $tier1Kw = floatval($company->setting('central_subsidy_tier1_max_kw', 2));
        $tier1Rate = floatval($company->setting('central_subsidy_tier1_rate', 30000));
        $tier2Kw = floatval($company->setting('central_subsidy_tier2_max_kw', 3));
        $tier2Rate = floatval($company->setting('central_subsidy_tier2_rate', 18000));

        if ($kw <= $tier1Kw) {
            $central = $kw * $tier1Rate;
        } else {
            $base = $tier1Kw * $tier1Rate;
            $extra = min(max(0, $tier2Kw - $tier1Kw), $kw - $tier1Kw) * $tier2Rate;
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
