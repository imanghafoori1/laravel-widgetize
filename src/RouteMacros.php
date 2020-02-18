<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Route;

class RouteMacros
{
    public function registerMacros()
    {
        $this->registerWidget();

        $this->registerJsonWidget();
    }

    private function registerWidget(): void
    {
        Route::macro('widget', function ($url, $widget, $name = null) {
            return Route::get($url, [
                'as' => $name,
                'uses' => function (...$args) use ($widget) {
                    return render_widget($widget, $args);
                },
            ]);
        });
    }

    private function registerJsonWidget(): void
    {
        Route::macro('jsonWidget', function ($url, $widget, $name = null) {
            return Route::get($url, [
                'as' => $name,
                'uses' => function (...$args) use ($widget) {
                    return json_widget($widget, $args);
                },
            ]);
        });
    }
}
