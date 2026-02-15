<?php

namespace Skywalker\Location\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Support\Http\Concerns\ApiResponse;

class TorBlocker
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only verify if enforcement is enabled
        if (config('location.tor.block', false)) {
            $position = Location::get();

            if ($position && $position->isTor) {
                return $this->apiError('Access Denied: Tor Network not allowed.', 403);
            }
        }

        return $next($request);
    }
}
