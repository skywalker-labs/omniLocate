<div align="center">

# 📍 OmniLocate: The Toolkit-Powered Location Intelligence

### *Professional GEO-Resolution, Risk Scoring, and Hybrid Verification for Laravel 12+*

[![Latest Version](https://img.shields.io/badge/version-2.0.0-blueviolet.svg?style=for-the-badge)](https://packagist.org/packages/skywalker-labs/location)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge)](https://laravel.com)
[![Toolkit Foundation](https://img.shields.io/badge/Toolkit-v1.3.0-success.svg?style=for-the-badge)](https://github.com/skywalker-labs/toolkit)

---

**OmniLocate** is an elite geographical intelligence library built on the **Skywalker Toolkit Foundation**. It provides standardized API responses, multi-driver failover, and advanced security features like Tor blocking and hybrid location verification.

</div>

---

## 🌍 Why OmniLocate?

1.  **Toolkit Foundation:** Leveraging `skywalker-labs/toolkit` for standardized API responses (`ApiResponse`), enhanced I/O (`Command`), and prefixed database models.
2.  **Multi-Driver Resilience:** Automatic failover between 8+ native drivers (MaxMind, IPInfo, IpApi, etc.) to ensure location data is always available.
3.  **Hybrid Verification:** Combines IP geolocation with real-time GPS coordinates to detect spoofing and VPN usage.
4.  **Geo-Fencing & Risk Guard:** Built-in middleware to restrict traffic or block high-risk IPs based on customizable scoring.
5.  **Analytics Dashboard:** A full-featured dashboard for monitoring requests, threats, and geographical trends.

---

## ⚡ Performance & Standards

| Feature | Legacy | OmniLocate (v2.0+) |
| :--- | :--- | :--- |
| **Foundation** | Custom | **Skywalker Toolkit** |
| **API Format** | Flat JSON | **Standardized JSON Wrapper** |
| **Lookup Speed** | 200ms | **~40ms (Cached)** |
| **Model Support** | Eloquent | **Prefixed Models (location_*)** |
| **Verification** | IP only | **Hybrid (IP + GPS)** |

---

## 🚀 Installation

Install the package via composer:

```bash
composer require skywalker-labs/location
```

Publish the configuration, assets, and migrations:

```bash
php artisan vendor:publish --provider="Skywalker\Location\LocationServiceProvider"
```

Run the migrations for GeoAnalytics:

```bash
php artisan migrate
```

---

## 🛠️ Usage

### Basic Location Trace
```php
use Skywalker\Location\Facades\Location;

$position = Location::get(); // Auto-detects IP

echo $position->countryName;   // "United States"
echo $position->currencyCode;  // "USD"
echo $position->latitude;      // "40.7128"
```

### Hybrid Verification (GPS + IP)
Perfect for mobile-web apps that need to ensure users are where they say they are.

```php
use Skywalker\Location\Services\HybridVerifier;

$verifier = new HybridVerifier();
$result = $verifier->verify($ip, $latitude, $longitude);

if ($result['is_spoofed']) {
    // Handle spoofing/VPN usage
}
```

### Standardized API Responses
All package controllers return standardized responses via the Toolkit's `ApiResponse` trait:

```json
{
  "status": "success",
  "message": "Location verification completed",
  "data": {
    "verified": true,
    "is_spoofed": false,
    "distance_km": 12.5,
    "ip_location": { ... },
    "gps_location": { ... }
  }
}
```

---

## 🛡️ Middleware Power

OmniLocate ships with powerful security middleware:

-   `TorBlocker`: Blocks requests originating from Tor exit nodes.
-   `GeoRestriction`: Restricts access to specific countries or continents.
-   `GeoRiskGuard`: Blocks IPs with risk scores above your threshold.
-   `BotVerifier`: Verifies legitimate crawlers (Google, Bing) to avoid accidental blocking.

```php
// routes/web.php
Route::middleware(['location.tor', 'location.risk:70'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index']);
});
```

---

## 📊 GeoAnalytics Dashboard

Enable the dashboard in `config/location.php` to monitor your traffic in real-time.

-   **Path:** `/omni-locate/dashboard`
-   **Features:** Total requests, blocked threats, top countries, and live log stream.

---

## 🔧 Drivers Supported

OmniLocate supports a wide array of drivers out of the box:
- `IpApi` (Default)
- `IpInfo`
- `IpData`
- `MaxMind` (Database & Web Service)
- `GeoPlugin`
- `HttpHeader` (For Cloudflare/Varnish upstream headers)

---

Created & Maintained by **Skywalker-Labs Team**.
Distributed under the MIT License.
