# Hybrid Geolocation (The "Omni" Factor) 🛰️

Traditional IP geolocation is flawed—anyone can buy a VPN.
**Hybrid Geolocation** solves this by comparing **Logical Location (IP)** vs **Physical Location (GPS)**.

## How it works

1. **Frontend**: Browser requests GPS permission (`navigator.geolocation`).
2. **Backend**: Receives GPS coordinates + IP.
3. **Calculation**: Calculates distance between IP-Location and GPS-Location.
4. **Verdict**: If distance > Threshold (e.g. 500km), it is **Spoofing**.

## Setup

### 1. Frontend Integration

Use the included zero-dependency JS helper.

```javascript
import OmniLocate from "./vendor/ermradulsharma/omnilocate/js/omni-locate.js";

const locator = new OmniLocate();

locator.verify((result) => {
  if (result.is_spoofed) {
    console.warn("⚠️ Spoofing Detected!");
    console.warn("IP says: " + result.ip_location.city);
    console.warn(
      "GPS says: " + result.gps_location.lat + "," + result.gps_location.lon,
    );
    console.warn("Distance: " + result.distance_km + "km");
  } else {
    console.log("✅ Verified User.");
  }
});
```

### 2. Backend Verification Endpoint

The package includes a route: `POST /omni-locate/verify`.

**Response:**

```json
{
    "verified": false,
    "is_spoofed": true,
    "distance_km": 6500.42,
    "threshold_km": 500,
    "ip_location": { ... },
    "gps_location": { ... }
}
```

### 3. Manual Usage

You can use the service directly in your controllers:

```php
use Skywalker\Location\Services\HybridVerifier;

public function check(Request $request, HybridVerifier $verifier)
{
    $result = $verifier->verify($request->ip(), $request->lat, $request->lon);

    if ($result['is_spoofed']) {
        abort(403, 'Location Verification Failed');
    }
}
```

