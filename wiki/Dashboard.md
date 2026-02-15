# Visual Intelligence Dashboard 📊

OmniLocate includes a **Zero-Code Dashboard** to visualize your traffic, threats, and blocked requests.

## Access

Visit: `config('app.url')/omni-locate/dashboard`

## Features

1. **Live Threat Map**: Vector map showing traffic sources.
2. **KPI Cards**:
   - Total Requests
   - Blocked Threats (High Risk)
   - Top Country
3. **Risk Distribution**: Pie Chart (Low vs Medium vs High Risk).
4. **Live Logs**: Real-time table of incoming requests with risk info.

## Configuration

### Enable/Disable

You can toggle the dashboard in `config/location.php`:

```php
'dashboard' => [
    'enabled' => true,
],
```

### Security / Authentication

By default, the dashboard uses the `web` middleware group. To restrict access (e.g., to admins only), you should wrap the route in your own gateway or middleware.

Since this is a package route, you can override it in your `routes/web.php` if needed, or stick to IP-based whitelisting via the `GeoRestriction` middleware on the dashboard route itself (requires custom route definition).

_Future updates will allow configuring middleware for the dashboard route directly._

