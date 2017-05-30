
<?php

if (!function_exists('render_widget')) {
    function render_widget($widget, ...$args)
    {
        return app(\Imanghafoori\Widgets\Utils\WidgetRenderer::class)->renderWidget($widget, ...$args);
    }
}

if (!function_exists('expire_widget')) {
    function expire_widgets($tags)
    {
        return app(\Imanghafoori\Widgets\Utils\Cache::class)->expireTaggedWidgets($tags);
    }
}
