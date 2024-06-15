<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Blade;

/**
 * @codeCoverageIgnore
 */
class BladeDirective
{
    private static $expression;

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
    public static function defineDirectives()
    {
        $omitParenthesis = version_compare(app()->version(), '5.3', '<');

        self::defineWidgetDirective($omitParenthesis);

        self::defineSlotDirectives($omitParenthesis);
    }

    /**
     * | ------------------------------------------ |
     * |      Define Widgetize Slots Directives     |
     * | ------------------------------------------ |
     * | When you call @ slot from your widget      |
     * | The only thing that happens is that the    |
     * | `renderSlot` method Gets called on the     |
     * | `Utils\SlotRenderer` trait                 |
     * | ------------------------------------------ |.
     */
    private static function defineSlotDirectives($omitParenthesis)
    {
        Blade::directive('slot', function ($slotName) use ($omitParenthesis) {
            $slotName = $omitParenthesis ? $slotName : "($slotName)";

            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->startSlot{$slotName};?>";
        });

        Blade::directive('endSlot', function () {
            $contentKey = '$content';

            return "<?php 
                        $contentKey = ob_get_clean();
                        echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderSlot($contentKey);
                    ?>";
        });

        Blade::directive('slotWidget', function ($expression) use ($omitParenthesis) {
            self::$expression = $omitParenthesis ? $expression : "($expression)";
        });

        Blade::directive('endSlotWidget', function () {
            $expression = self::$expression;

            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
        });
    }

    private static function defineWidgetDirective($omitParenthesis): void
    {
        Blade::directive('widget', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app(\\Imanghafoori\\Widgets\\Utils\\WidgetRenderer::class)->renderWidget{$expression}; ?>";
        });
    }
}
