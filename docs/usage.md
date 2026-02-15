# Basic Usage

## 📍 Retrieving Location

### Current User

Get the location of the visitor making the current request:

```php
use Skywalker\Location\Facades\Location;

$position = Location::get(); // Returns verified Position object
```

### Specific IP

Get location for any IP address:

```php
$position = Location::get('8.8.8.8');
```

## 📄 The Position Object

The `Position` object contains all available intelligence:

| Property       | Description            | Example                 |
| :------------- | :--------------------- | :---------------------- |
| `countryName`  | Full country name      | `"United States"`       |
| `countryCode`  | ISO 3166-1 Alpha-2     | `"US"`                  |
| `cityName`     | City                   | `"San Francisco"`       |
| `latitude`     | GPS Latitude           | `37.7749`               |
| `longitude`    | GPS Longitude          | `-122.4194`             |
| `currencyCode` | Local Currency         | `"USD"`                 |
| `timezone`     | Timezone               | `"America/Los_Angeles"` |
| `isProxy`      | Is using Proxy? (bool) | `false`                 |
| `isVpn`        | Is using VPN? (bool)   | `true`                  |
| `geoRiskScore` | Fraud Risk (0-100)     | `85`                    |

## 📐 Distance Calculations

Calculate the Haversine distance between two locations.

```php
$pos1 = Location::get('1.2.3.4');
$pos2 = Location::get('5.6.7.8');

// Get distance in Kilometers
$km = $pos1->distanceTo($pos2);

// Get distance in Miles
$miles = $pos1->distanceTo($pos2, 'miles');
```

## 🖥️ Blade Directives

Use directly in your views:

```blade
@location('countryName')
<!-- Output: United States -->

@if(@location('isVpn'))
    <div class="alert">VPN User Detected</div>
@endif
```


