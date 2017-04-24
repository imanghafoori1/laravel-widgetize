<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Imanghafoori\Widgets\Utils\Normalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
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
        $this->_defineDirectives();
        $this->loadViewsFrom($this->app->basePath().'/app/Widgets/', 'Widgets');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.imanghafoori.widget', function ($app) {
            return $app['Imanghafoori\Widgets\WidgetGenerator'];
        });

        $this->app->singleton(Normalizer::class, function () {
            $cacheNormalizer = new CacheNormalizer();
            $templateNormalizer = new TemplateNormalizer();
            $presenterNormalizer = new PresenterNormalizer();
            $controllerNormalizer = new ControllerNormalizer();

            return new Utils\Normalizer($templateNormalizer, $cacheNormalizer, $presenterNormalizer, $controllerNormalizer);
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

        $this->app->singleton(Utils\WidgetRenderer::class, function () {
            return new Utils\WidgetRenderer();
        });

        $this->commands('command.imanghafoori.widget');
    }

    /**
     * Define Blade Directives
     */
    private function _defineDirectives()
    {
        $omitParenthesis = version_compare($this->app->version(), '5.3', '<');

        Blade::directive('widget', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
        });
    }
}
