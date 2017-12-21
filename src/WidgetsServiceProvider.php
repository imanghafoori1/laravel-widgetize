<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use DebugBar\DataCollector\MessagesCollector;

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
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('widgetize.php'),
        ]);

        $this->defineDirectives();
        $this->loadViewsFrom($this->app->basePath().'/app/Widgets/', 'Widgets');
    }

    /**
     * | ------------------------------------------ |
     * |         Define Blade Directive             |
     * | ------------------------------------------ |
     * | When you call @ widget from your views     |
     * | The only thing that happens is that the    |
     * | `renderWidget` method Gets called on the   |
     * | `Utils\WidgetRenderer` class               |
     * | ------------------------------------------ |.
     */
    private function defineDirectives()
    {
        $omitParenthesis = version_compare($this->app->version(), '5.3', '<');

        Blade::directive('widget', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
        });
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
}
