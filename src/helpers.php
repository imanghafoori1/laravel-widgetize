<?php

if (!function_exists('render_widget')) {
    function render_widget($widget, ...$args)
    {
        return app(\Imanghafoori\Widgets\Utils\WidgetRenderer::class)->renderWidget($widget, ...$args);
    }
}
