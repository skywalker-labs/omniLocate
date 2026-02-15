# Active Security Shield 🛡️

OmniLocate transforms your application into a fortress using Deep IP Intelligence.

## 🤖 Smart Bot Verification

Don't just trust the `User-Agent`. We verify bots using **Reverse DNS**.

**How it works:**

1. Check if User-Agent claims to be "Googlebot".
2. Perform Reverse DNS on the IP.
3. Validate domain suffix (e.g., must end in `.googlebot.com`).

**Usage:**
Protect sensitive routes to allow ONLY real humans and VERIFIED bots.

```php
Route::middleware(\Skywalker\Location\Middleware\BotVerifier::class)->group(function () {
    Route::get('/sitemap.xml', 'SitemapController@index'); // Verified bots allowed
    Route::get('/admin', 'AdminController@index'); // Humans allowed, Fake bots blocked
});
```

**Config:**

```php
'bots' => [
    'enabled' => true,
    'trusted_domains' => [ ... ]
]
```

## 🚨 Geo Risk Guard

Automatically block high-risk traffic (Fraud/Abuse).

**Usage:**

```php
// Block anyone with Risk Score > 80 (Confugrable in config)
Route::middleware(\Skywalker\Location\Middleware\GeoRiskGuard::class)->group(function () {
    Route::post('/payment', 'PaymentController@process');
});
```

## ⚡ Adaptive Rate Limiting

Throttle suspicious users without punishing legitimate ones.

| User Type           | Rate Limit                 |
| :------------------ | :------------------------- |
| **Low Risk** (<30)  | 60 req/min                 |
| **High Risk** (>70) | 5 req/min                  |
| **Verified Bot**    | 1000 req/min (Whitelisted) |

**Usage:**

```php
Route::middleware(\Skywalker\Location\Middleware\AdaptiveRateLimit::class)->group(function () {
    Route::any('/api/*', ...);
});
```

## 🧅 Tor Blocker

Block anonymous traffic from the Tor network.

**Config:**

```php
'tor' => [ 'block' => true ]
```

