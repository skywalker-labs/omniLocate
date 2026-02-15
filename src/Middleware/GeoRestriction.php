<?php

namespace Skywalker\Location\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;

class GeoRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $position = Location::get();

        if ($position) {
            $allowed = config('location.restriction.allowed_countries', []);
            $blocked = config('location.restriction.blocked_countries', []);

            if (!empty($allowed) && !in_array($position->countryCode, $allowed)) {
                return response('Access Denied from your country.', 403);
            }

            if (!empty($blocked) && in_array($position->countryCode, $blocked)) {
                return response('Access Denied from your country.', 403);
            }
        }

        return $next($request);
    }
}

