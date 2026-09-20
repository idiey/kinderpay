<?php

namespace App\Services;

class StatutoryService
{
    /**
     * Calculate EPF employee contribution (standard 11%).
     */
    public function epfEmployee(float $wage, int $category = 1): float
    {
        if ($category === 2) {
            return 0.0; // Age 60+ employee share is 0%
        }
        return round($wage * 0.11, 2);
    }

    /**
     * Calculate EPF employer contribution (12% if wage > 5000, 13% if wage <= 5000).
     */
    public function epfEmployer(float $wage, int $category = 1): float
    {
        if ($category === 2) {
            return round($wage * 0.04, 2); // Age 60+ employer share is 4%
        }

        $rate = $wage > 5000.0 ? 0.12 : 0.13;
        return round($wage * $rate, 2);
    }

    /**
     * Calculate SOCSO employee contribution (approx 0.5%, wage capped at RM5,000).
     */
    public function socsoEmployee(float $wage): float
    {
        $insurableWage = min($wage, 5000.0);
        return round($insurableWage * 0.005, 2);
    }

    /**
     * Calculate SOCSO employer contribution (approx 1.75%, wage capped at RM5,000).
     */
    public function socsoEmployer(float $wage): float
    {
        $insurableWage = min($wage, 5000.0);
        return round($insurableWage * 0.0175, 2);
    }

    /**
     * Calculate EIS employee contribution (0.2%, wage capped at RM5,000).
     */
    public function eisEmployee(float $wage): float
    {
        $insurableWage = min($wage, 5000.0);
        return round($insurableWage * 0.002, 2);
    }

    /**
     * Calculate EIS employer contribution (0.2%, wage capped at RM5,000).
     */
    public function eisEmployer(float $wage): float
    {
        $insurableWage = min($wage, 5000.0);
        return round($insurableWage * 0.002, 2);
    }
}
