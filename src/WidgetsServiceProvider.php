<?php

namespace Imanghafoori\Widgets;

use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider
{
    private $expression;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->_registerDebugbar();
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('widgetize.php'),
        ]);

        $this->defineDirectives();
        $this->loadViewsFrom($this->app->basePath() . '/app/Widgets/', 'Widgets');
    }

    /**
     * | ------------------------------------------ |
     * |         Define Blade Directives            |
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
            if (strpos($expression, 'slotable') == false) {
                return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
            }
            $this->expression = preg_replace('/\bslotable\b/u', '', $expression);
        });

        $this->defineSlotDirectives($omitParenthesis);

        Blade::directive('endwidget', function () {
            $expression = $this->expression;
            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
        });
    }

    /**
     * | ------------------------------------------ |
     * |       Define Wdgetize Slots Directives     |
     * | ------------------------------------------ |
     * | When you call @ slot from your widget      |
     * | The only thing that happens is that the    |
     * | `renderSlot` method Gets called on the     |
     * | `Utils\SlotRenderer` trait                 |
     * | ------------------------------------------ |.
     */
    private function defineSlotDirectives($omitParenthesis)
    {
        Blade::directive('slot', function ($slotName) use ($omitParenthesis) {
            $slotName = $omitParenthesis ? $slotName : "($slotName)";
            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->startSlot{$slotName};?>";
        });

        Blade::directive('endslot', function () {
            $contentKey = '$content';
            return "<?php 
                        $contentKey = ob_get_clean();
                        echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderSlot($contentKey);
                    ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'widgetize');
        $this->commands('command.imanghafoori.widget');
        app(RouteMacros::class)->registerMacros();
        app(SingletonServices::class)->registerSingletons($this->app);
    }

    private function _registerDebugbar()
    {
        if (!$this->app->offsetExists('debugbar')) {
            return;
        }

        $this->app->singleton('widgetize.debugger', function () {
            return new MessagesCollector('Widgets');
        });

        $this->app->make('debugbar')->addCollector(app('widgetize.debugger'));
    }
}
