<?php

namespace Skywalker\Location\Services;

use Skywalker\Location\Facades\Location;
use Skywalker\Location\Position;

class HybridVerifier
{
    /**
     * Verify if the user's physical location matches their IP location.
     *
     * @param string $ip
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function verify($ip, $latitude, $longitude)
    {
        $ipPosition = Location::get($ip);

        if (!$ipPosition || $ipPosition->isEmpty()) {
            return [
                'verified' => false,
                'reason' => 'IP location not found',
                'distance' => null,
            ];
        }

        // Create a temporary Position for the GPS coordinates
        $gpsPosition = new Position();
        $gpsPosition->latitude = $latitude;
        $gpsPosition->longitude = $longitude;

        // Calculate distance in kilometers
        $distance = $ipPosition->distanceTo($gpsPosition);

        // Get threshold from config or default to 500km
        $threshold = config('location.hybrid.threshold', 500);

        $isSpoofed = $distance > $threshold;

        return [
            'verified' => !$isSpoofed,
            'is_spoofed' => $isSpoofed,
            'distance_km' => round($distance, 2),
            'threshold_km' => $threshold,
            'ip_location' => [
                'city' => $ipPosition->cityName,
                'country' => $ipPosition->countryCode,
                'lat' => $ipPosition->latitude,
                'lon' => $ipPosition->longitude,
            ],
            'gps_location' => [
                'lat' => $latitude,
                'lon' => $longitude,
            ],
        ];
    }
}

