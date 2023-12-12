<?php

namespace Imanghafoori\Widgets;

use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->_registerDebugbar();
        $this->setPublishes();
        BladeDirective::defineDirectives();
        $this->loadViewsFrom($this->app->basePath().'/app/Widgets/', 'Widgets');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'widgetize');
        $this->commands('command.imanghafoori.widget');
        app(RouteMacros::class)->registerMacros();
        app(SingletonServices::class)->registerSingletons($this->app);
    }

    private function _registerDebugbar()
    {
        if (! $this->app->offsetExists('debugbar')) {
            return;
        }

        $this->app->singleton('widgetize.debugger', function () {
            return new MessagesCollector('Widgets');
        });

        $this->app->make('debugbar')->addCollector(app('widgetize.debugger'));
    }

    private function setPublishes()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('widgetize.php'),
        ]);
    }
}
