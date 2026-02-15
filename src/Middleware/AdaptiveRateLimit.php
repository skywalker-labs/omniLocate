<?php

namespace Skywalker\Location\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Illuminate\Routing\Middleware\ThrottleRequests;

class AdaptiveRateLimit extends ThrottleRequests
{
    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return sha1($request->ip()); // Simple IP based signature
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  float|int  $decayMinutes
     * @param  string  $prefix
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Calculate maxAttempts based on Risk
        $position = Location::get();

        if ($position && $position->geoRiskScore !== null) {
            $risk = $position->geoRiskScore;

            if ($risk >= 70) {
                $maxAttempts = 5; // Very strict for high risk
            } elseif ($risk >= 30) {
                $maxAttempts = 20; // Restricted for medium risk
            } else {
                $maxAttempts = 100; // Generous for low risk
            }
        }

        // If it's a Verified Bot, give them a high limit or bypass
        if (Location::isVerifiedBot()) {
            $maxAttempts = 1000;
        }

        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}

