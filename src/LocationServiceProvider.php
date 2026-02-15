<?php

namespace Skywalker\Location;

use Illuminate\Support\Str;
use Skywalker\Support\Providers\PackageServiceProvider;

class LocationServiceProvider extends PackageServiceProvider
{
    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'skywalker-labs';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'location';

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        parent::boot();

        if ($this->isLumen()) {
            return;
        }

        $this->publishAll();

        if ($this->app['config']->get('location.dashboard.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
        }

        $this->loadViews();
        $this->registerValidationRules();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->app->singleton('location', function ($app) {
            return new Location($app['config']);
        });

        $this->registerCommands([\Skywalker\Location\Commands\UpdateMaxMindDatabase::class]);
    }

    /**
     * Register the validation rules.
     */
    protected function registerValidationRules(): void
    {
        if (! $this->app->has('validator')) {
            return;
        }

        $this->app['validator']->extend('location', function ($attribute, $value, $parameters) {
            return (new \Skywalker\Location\Rules\LocationRule($parameters[0] ?? ''))->passes($attribute, $value);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['location'];
    }

    /**
     * Register the package's custom blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        parent::registerBladeDirectives();

        if (! $this->app->has('blade.compiler')) {
            return;
        }

        $this->app['blade.compiler']->directive('location', function ($expression) {
            return "<?php if (\$position = \Skywalker\Location\Facades\Location::get()): ?>
                <?php echo \$position->{$expression} ?? \$position; ?>
            <?php endif; ?>";
        });
    }

    /**
     * Determine if the current application is Lumen.
     *
     * @return bool
     */
    protected function isLumen(): bool
    {
        return Str::contains($this->app->version(), 'Lumen');
    }
}
