<?php

namespace Skywalker\Location;

use Illuminate\Contracts\Config\Repository;
use Skywalker\Location\Drivers\Driver;
use Skywalker\Location\Exceptions\DriverDoesNotExistException;

class Location
{
    /**
     * The current driver.
     *
     * @var Driver
     */
    protected $driver;

    /**
     * The application configuration.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Repository $config
     *
     * @throws DriverDoesNotExistException
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->setDefaultDriver();
    }

    /**
     * Set the current driver to use.
     *
     * @param Driver $driver
     */
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the default location driver to use.
     *
     * @throws DriverDoesNotExistException
     */
    public function setDefaultDriver()
    {
        $driver = $this->getDriver($this->getDefaultDriver());

        foreach ($this->getDriverFallbacks() as $fallback) {
            $driver->fallback($this->getDriver($fallback));
        }

        $this->setDriver($driver);
    }

    /**
     * Add a fallback driver.
     *
     * @param Driver $driver
     * @return void
     */
    public function fallback(Driver $driver)
    {
        $this->driver->fallback($driver);
    }

    /**
     * Attempt to retrieve the location of the user.
     *
     * @param string|null $ip
     *
     * @return \Skywalker\Location\Position|bool
     */
    public function get($ip = null)
    {
        if ($this->isBot()) {
            return false;
        }

        $ip = $ip ?: $this->getClientIP();

        $position = $this->cacheEnabled()
            ? cache()->remember($this->getCacheKey($ip), $this->getCacheDuration(), function () use ($ip) {
                return $this->driver->get($ip);
            })
            : $this->driver->get($ip);

        if ($position) {
            $this->hydrateAdvancedFeatures($position);

            event(new Events\LocationDetected($position));

            return $position;
        }

        return false;
    }

    /**
     * Hydrate advanced features on the position.
     *
     * @param Position $position
     * @return void
     */
    protected function hydrateAdvancedFeatures(Position $position)
    {
        $position->currencyCode = $this->getCurrencyCode($position->countryCode);
        $position->language = $this->getLanguageCode($position->countryCode);

        // Simple heuristic for connection type if not provided by driver
        if (!$position->connectionType && $position->ip) {
            // This is very basic, a real implementation would need a database
            $position->connectionType = 'Unknown';
        }
    }

    /**
     * Get the currency code for the given country code.
     *
     * @param string|null $countryCode
     * @return string|null
     */
    protected function getCurrencyCode($countryCode)
    {
        $currencies = [
            'US' => 'USD',
            'IN' => 'INR',
            'GB' => 'GBP',
            'CA' => 'CAD',
            'AU' => 'AUD',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'JP' => 'JPY',
            'BR' => 'BRL',
            'CN' => 'CNY',
            'RU' => 'RUB',
        ];

        return $currencies[strtoupper($countryCode)] ?? null;
    }

    /**
     * Get the language code for the given country code.
     *
     * @param string|null $countryCode
     * @return string|null
     */
    protected function getLanguageCode($countryCode)
    {
        $languages = [
            'US' => 'en',
            'GB' => 'en',
            'IN' => 'hi', // or en
            'DE' => 'de',
            'FR' => 'fr',
            'ES' => 'es',
            'IT' => 'it',
            'JP' => 'ja',
            'CN' => 'zh',
            'RU' => 'ru',
            'BR' => 'pt',
        ];

        return $languages[strtoupper($countryCode)] ?? null;
    }

    /**
     * Determine if the current user is a bot.
     *
     * @return bool
     */
    protected function isBot()
    {
        if (! $this->config->get('location.bots.enabled', false)) {
            return false;
        }

        $agent = request()->userAgent();

        foreach ($this->config->get('location.bots.list', []) as $bot) {
            if (str_contains(strtolower($agent), strtolower($bot))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current user is a VERIFIED bot (e.g., real Googlebot).
     *
     * @return bool
     */
    public function isVerifiedBot()
    {
        if (!$this->isBot()) {
            return false;
        }

        $ip = request()->ip();
        $agent = strtolower(request()->userAgent());

        // Skip verification for local testing IPs if needed, or handle gracefully
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Cache the verification to avoid slow DNS lookups on every request
        return cache()->remember("bot_verified.$ip", 3600, function () use ($ip, $agent) {
            $hostname = gethostbyaddr($ip);

            if (!$hostname || $hostname === $ip) {
                return false;
            }

            // Check against trusted domains based on User-Agent
            // This config should be added to location.php
            $trusted = $this->config->get('location.bots.trusted_domains', [
                'googlebot' => ['.googlebot.com', '.google.com'],
                'bingbot' => ['.search.msn.com'],
                'slurp' => ['.crawl.yahoo.net'],
                'duckduckbot' => ['.duckduckgo.com'],
                'yandexbot' => ['.yandex.com', '.yandex.ru', '.yandex.net'],
                'baiduspider' => ['.baidu.com', '.baidu.jp'],
            ]);

            foreach ($trusted as $botKey => $domains) {
                if (str_contains($agent, $botKey)) {
                    foreach ($domains as $domain) {
                        if (str_ends_with($hostname, $domain)) {
                            // Double check: forward DNS
                            // This prevents "fake-google.com" pointing to a malicious IP,
                            // though strictly checking endswith .googlebot.com is usually safe-ish
                            // if the top-level domain is controlled.
                            // For maximum security, resolve the hostname back to IP.
                            $resolvedIps = gethostbynamel($hostname);
                            if ($resolvedIps && in_array($ip, $resolvedIps)) {
                                return true;
                            }
                        }
                    }
                }
            }

            return false;
        });
    }

    /**
     * Determine if caching is enabled.
     *
     * @return bool
     */
    protected function cacheEnabled()
    {
        return $this->config->get('location.cache.enabled', false);
    }

    /**
     * Get the cache key for the given IP address.
     *
     * @param string $ip
     *
     * @return string
     */
    protected function getCacheKey($ip)
    {
        return "location.$ip";
    }

    /**
     * Get the cache duration.
     *
     * @return int
     */
    protected function getCacheDuration()
    {
        return $this->config->get('location.cache.duration', 86400);
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    protected function getClientIP()
    {
        return $this->localHostTesting()
            ? $this->getLocalHostTestingIp()
            : request()->ip();
    }

    /**
     * Determine if testing is enabled.
     *
     * @return bool
     */
    protected function localHostTesting()
    {
        return $this->config->get('location.testing.enabled', true);
    }

    /**
     * Get the testing IP address.
     *
     * @return string
     */
    protected function getLocalHostTestingIp()
    {
        return $this->config->get('location.testing.ip', '66.102.0.0');
    }

    /**
     * Get the fallback location drivers to use.
     *
     * @return array
     */
    protected function getDriverFallbacks()
    {
        return $this->config->get('location.fallbacks', []);
    }

    /**
     * Get the default location driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->config->get('location.driver');
    }

    /**
     * Attempt to create the location driver.
     *
     * @param string $driver
     *
     * @return Driver
     *
     * @throws DriverDoesNotExistException
     */
    protected function getDriver($driver)
    {
        if (! class_exists($driver)) {
            throw DriverDoesNotExistException::forDriver($driver);
        }

        return app()->make($driver);
    }
}

