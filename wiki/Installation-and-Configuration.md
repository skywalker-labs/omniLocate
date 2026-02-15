# Installation & Configuration

## Installation

Install OmniLocate via Composer:

```bash
composer require ermradulsharma/omnilocate
```

The package will automatically register its service provider and facade.

## Configuration

Publish the configuration file to customize your drivers and settings:

```bash
php artisan vendor:publish --provider="Skywalker\Location\LocationServiceProvider"
```

This creates `config/location.php` and the migration file.

### Migrations

OmniLocate requires a database table to store analytics and logs.

```bash
php artisan migrate
```

### Configuration Options

The configuration file allows you to set up drivers, fallbacks, caching, and more.

#### Default Driver

Set the default driver used for location retrieval.

```php
'driver' => Skywalker\Location\Drivers\HttpHeader::class,
```

#### Fallbacks

Define a list of drivers to use if the default driver fails.

```php
'fallbacks' => [
    Skywalker\Location\Drivers\IpApi::class,
    Skywalker\Location\Drivers\IpInfo::class,
    // ...
],
```

#### Caching

Enable caching to store location results and reduce API calls.

```php
'cache' => [
    'enabled' => env('LOCATION_CACHE', false),
    'duration' => 86400, // 24 hours
],
```

#### Bot Detection

Skip location detection for bots to save resources.

```php
'bots' => [
    'enabled' => true,
    'list' => [ 'googlebot', 'bingbot', ... ],
    'trusted_domains' => [ ... ],
],
```

#### Dashboard

Enable or disable the visual intelligence dashboard.

```php
'dashboard' => [
    'enabled' => true,
],
```

### API Keys

Some drivers require API keys. You should add these to your `.env` file and reference them in `config/location.php`.

```env
IP_API_TOKEN=your-token-here
IPINFO_TOKEN=your-token-here
IPDATA_TOKEN=your-token-here
```

Refer to the [Drivers](Drivers) page for detailed configuration for each driver.

