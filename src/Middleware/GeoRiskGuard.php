<?php

namespace Skywalker\Location\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Support\Http\Concerns\ApiResponse;

class GeoRiskGuard
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null $threshold
     * @return mixed
     */
    public function handle($request, Closure $next, $threshold = null)
    {
        $position = Location::get();

        if ($position && $position->geoRiskScore !== null) {
            $threshold = $threshold ?: config('location.risk.threshold', 80);

            if ($position->geoRiskScore >= $threshold) {
                return $this->apiError('Access Denied: High Risk IP.', 403);
            }
        }

        return $next($request);
    }
}
