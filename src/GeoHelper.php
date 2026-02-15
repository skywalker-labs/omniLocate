<?php

namespace Skywalker\Location;

use Skywalker\Location\Facades\Location;

class GeoHelper
{
    /**
     * Get a rate limit key based on the user's country.
     *
     * @param string $prefix
     * @return string
     */
    public static function rateLimitKey($prefix = 'geo_limit')
    {
        $position = Location::get();
        $country = $position ? $position->countryCode : 'unknown';
        $ip = $position ? $position->ip : request()->ip();

        return "{$prefix}:{$country}:{$ip}";
    }

    /**
     * Check if the user is in a high risk country.
     *
     * @return bool
     */
    public static function isHighRiskCountry()
    {
        $position = Location::get();
        if (!$position) return false;

        $highRisk = config('location.risk.high_risk_countries', []);
        return in_array($position->countryCode, $highRisk);
    }

    /**
     * Get the recommended rate limit points based on risk.
     * 
     * @return int
     */
    public static function getRateLimitPoints()
    {
        $position = Location::get();
        // Default safe limit
        if (!$position) return 60;

        if (Location::isVerifiedBot()) return 1000;

        $risk = $position->geoRiskScore ?? 0;

        if ($risk >= 70) return 5;
        if ($risk >= 30) return 20;

        return 100;
    }
}

