<?php

namespace Skywalker\Location\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Skywalker\Location\Services\HybridVerifier;

class HybridLocationController extends Controller
{
    /**
     * Verify location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Skywalker\Location\Services\HybridVerifier  $verifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, HybridVerifier $verifier)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $ip = $request->ip();

        // Allow passing IP for testing purposes if configured (careful with security)
        if (config('app.debug') && $request->has('test_ip')) {
            $ip = $request->input('test_ip');
        }

        $result = $verifier->verify(
            $ip,
            $request->input('latitude'),
            $request->input('longitude')
        );

        // Log to analytics if blocked/spoofed? 
        // Logic could be added here or via middleware.

        return response()->json($result);
    }
}

