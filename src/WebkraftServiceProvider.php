<?php

namespace Webkraft\Cms;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WebkraftServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/webkraft.php', 'webkraft');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'webkraft');

        // Convenience: every Webkraft view can reference the admin base path.
        $this->app['view']->share('webkraftBase', '/'.trim((string) config('webkraft.path', 'cms'), '/'));

        $this->registerRoutes();
        $this->registerPublishing();
    }

    protected function registerRoutes(): void
    {
        // Admin UI — yoursite.com/{path}
        Route::middleware(config('webkraft.middleware', ['web', 'auth']))
            ->prefix(config('webkraft.path', 'cms'))
            ->name('webkraft.')
            ->group(__DIR__.'/../routes/admin.php');

        // Public page rendering (catch-all by slug). Registered last so it
        // only catches what the host app hasn't already claimed.
        if (config('webkraft.public_routes', true)) {
            Route::middleware('web')
                ->group(__DIR__.'/../routes/public.php');
        }
    }

    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/webkraft.php' => config_path('webkraft.php'),
        ], 'webkraft-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/webkraft'),
        ], 'webkraft-views');
    }
}
