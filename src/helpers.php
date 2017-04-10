<?php
use Imanghafoori\Widgets\BaseWidget;

if (!function_exists('render_widget')) {
    function render_widget(BaseWidget $widget, ...$args)
    {
        return app('imanghafoori.widget.renderer')->renderWidget($widget, ...$args);
    }
}
