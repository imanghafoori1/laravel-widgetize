<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Imanghafoori\Widgets\Utils\Normalizer;
use DebugBar\DataCollector\MessagesCollector;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ContextAsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;

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
     * Define Blade Directives.
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
        $this->registerSingletons();
        $this->registerMacros();
    }

    /**
     * Register classes as singletons.
     */
    private function registerSingletons()
    {
        $this->app->singleton('command.imanghafoori.widget', function ($app) {
            return $app['Imanghafoori\Widgets\WidgetGenerator'];
        });

        $this->app->singleton(Normalizer::class, function () {
            $cacheNormalizer = new CacheNormalizer();
            $tplNormalizer = new TemplateNormalizer();
            $presenterNormalizer = new PresenterNormalizer();
            $ctrlNormalizer = new ControllerNormalizer();
            $contextAsNormalizer = new ContextAsNormalizer();

            return new Utils\Normalizer($tplNormalizer, $cacheNormalizer, $presenterNormalizer, $ctrlNormalizer, $contextAsNormalizer);
        });

        $this->app->singleton(Utils\HtmlMinifier::class, function () {
            return new Utils\HtmlMinifier();
        });

        $this->app->singleton(Utils\DebugInfo::class, function () {
            return new Utils\DebugInfo();
        });

        $this->app->singleton(Utils\Policies::class, function () {
            return new Utils\Policies();
        });

        $this->app->singleton(Utils\Cache::class, function () {
            return new Utils\Cache();
        });

        $this->app->singleton(Utils\CacheTag::class, function () {
            return new Utils\CacheTag();
        });

        $this->app->singleton(Utils\WidgetRenderer::class, function () {
            return new Utils\WidgetRenderer();
        });
    }

    private function registerMacros()
    {
        app(RouteMacros::class)->registerMacros();
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
