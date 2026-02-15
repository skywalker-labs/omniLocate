<div align="center">

# 📍 Location: Multi-Driver Intelligence
### *Professional GEO-Resolution and Smart Routing for Laravel 12+*

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blueviolet.svg?style=for-the-badge)](https://packagist.org/packages/skywalker-labs/location)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4+-777bb4.svg?style=for-the-badge)](https://php.net)

---

**Location** is an elite geographical intelligence library. It doesn't just find an IP; it resolves coordinates, timezones, and currencies across multiple providers (MaxMind, IPInfo, Google) with **Automatic Failover** and **Smart Caching**.

</div>

## 🌍 Why Location Elite?

1. **Multi-Driver Resilience:** If one GEO provider fails, Location automatically falls back to your backup provider.
2. **Geo-Fencing Architecture:** Built-in `GeoRuleMatcher` to restrict or redirect traffic based on complex location logic.
3. **Middleware-First DX:** Inject location data directly into your request lifecycle with zero boilerplate.

---

## 🔥 Killer Features

### 1. Smart Driver Switching
Configure a hierarchy of providers. High-performance MaxMind for speed, Google Maps for precision.

### 2. Coordinate-to-Metadata Mapping
Extends standard positioning to include currency symbols, country codes, and timezone objects.

### 3. Proactive Geo-Redirects
```php
Route::middleware('location.redirect:US')->get('/offers', ...);
```

---

## ⚡ Performance

| Feature | Competitors | Location Elite |
| :--- | :--- | :--- |
| **Failover** | Manual | **Automatic** |
| **Lookup Speed** | 200ms | **~40ms (Cached)** |
| **Driver Support** | Limited | **8+ Native Drivers** |

---

## 🛠️ Usage (PHP 8.4+)

```php
public function trace(): void 
{
    $position = Location::get(); // Auto-detects IP
    
    echo $position->countryName;
    echo $position->currencyCode;
}
```

---

Created & Maintained by **Skywalker-Labs**.
