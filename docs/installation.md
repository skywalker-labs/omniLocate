# Installation & Configuration

## 📦 Installation

Requirements:

- PHP >= 8.0
- Laravel >= 9.0

Install via Composer:

```bash
composer require ermradulsharma/omnilocate
```

## ⚙️ Configuration

### 1. Publish Assets

Publish the configuration file and database migrations:

```bash
php artisan vendor:publish --provider="Skywalker\Location\LocationServiceProvider"
```

### 2. Database Migrations

OmniLocate uses a database table (`geo_analytics`) to store traffic logs and intelligent analytics.

```bash
php artisan migrate
```

### 3. Setup Drivers

Open `config/location.php` (or `config.php`). Set your preferred drivers.

**Supported Drivers:**

- `MaxMind` (Local DB or Web Service)
- `IpApi` (Pro & Free)
- `IpInfo`
- `GeoPlugin`
- `HttpHeader` (Cloudflare/AWS headers)

Example `MaxMind` Local setup:

1. Download `GeoLite2-City.mmdb`.
2. Place it in `storage/app/` or `database/maxmind/`.
3. Update config path:
   ```php
   'maxmind' => [
       'local' => ['path' => database_path('maxmind/GeoLite2-City.mmdb')]
   ]
   ```


