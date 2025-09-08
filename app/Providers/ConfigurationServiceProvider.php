<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuration;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register configuration singleton
        $this->app->singleton('config.dynamic', function ($app) {
            return new Configuration();
        });

        // Helper function is now loaded from app/helpers.php
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share public configurations with all views
        try {
            $publicConfigs = Configuration::getPublic();
            View::share('publicConfigs', $publicConfigs);
        } catch (\Exception $e) {
            // Handle case where database/table doesn't exist yet
            View::share('publicConfigs', []);
        }

        // Register Blade directive for configuration access
        Blade::directive('config', function ($expression) {
            return "<?php echo dynamic_config({$expression}); ?>";
        });

        // Register Blade directive for checking if config exists
        Blade::directive('hasConfig', function ($expression) {
            return "<?php if(dynamic_config({$expression}) !== null): ?>";
        });

        Blade::directive('endHasConfig', function () {
            return "<?php endif; ?>";
        });
    }
}
