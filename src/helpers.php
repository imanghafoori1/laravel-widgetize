<?php

if (! function_exists('render_widget')) {
    function render_widget($widget, ...$args)
    {
        return app(\Imanghafoori\Widgets\Utils\WidgetRenderer::class)->renderWidget($widget, ...$args);
    }
}

if (! function_exists('expire_widgets')) {
    function expire_widgets($tags)
    {
        return app(\Imanghafoori\Widgets\Utils\Cache::class)->expireTaggedWidgets($tags);
    }
}

if (! function_exists('json_widget')) {
    function json_widget($widget, ...$args)
    {
        return app(\Imanghafoori\Widgets\Utils\WidgetJsonifier::class)->jsonResponse($widget, ...$args);
    }
}
