<?php

namespace Skywalker\Location\Tests;

use Mockery as m;
use Skywalker\Location\Position;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Services\HybridVerifier;

class HybridLocationTest extends TestCase
{
    public function test_hybrid_verification_spoofed()
    {
        // Mock Location::get('1.2.3.4') to return New York
        $ny = new Position();
        $ny->cityName = 'New York';
        $ny->latitude = 40.7128;
        $ny->longitude = -74.0060;
        $ny->countryCode = 'US';

        Location::shouldReceive('get')->with('1.2.3.4')->andReturn($ny);

        $verifier = new HybridVerifier();

        // Provided GPS is London (Spoofed VPN case)
        $londonLat = 51.5074;
        $londonLon = -0.1278;

        $result = $verifier->verify('1.2.3.4', $londonLat, $londonLon);

        $this->assertTrue($result['is_spoofed']);
        $this->assertFalse($result['verified']);
        $this->assertGreaterThan(5000, $result['distance_km']);
    }

    public function test_hybrid_verification_valid()
    {
        // Mock Location::get('1.2.3.4') to return New York
        $ny = new Position();
        $ny->cityName = 'New York';
        $ny->latitude = 40.7128;
        $ny->longitude = -74.0060;
        $ny->countryCode = 'US';

        Location::shouldReceive('get')->with('1.2.3.4')->andReturn($ny);

        $verifier = new HybridVerifier();

        // Provided GPS is nearby (Brooklyn)
        $brooklynLat = 40.6782;
        $brooklynLon = -73.9442;

        $result = $verifier->verify('1.2.3.4', $brooklynLat, $brooklynLon);

        $this->assertFalse($result['is_spoofed']);
        $this->assertTrue($result['verified']);
        $this->assertLessThan(20, $result['distance_km']);
    }
}

