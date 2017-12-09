<?php

namespace Imanghafoori\Widgets;

use Illuminate\Support\Facades\Route;

class RouteMacros
{
    public function registerMacros()
    {
        $this->registerViewMacro();

        $this->registerWidget();

        $this->registerJsonWidget();
    }

    private function registerViewMacro()
    {
        Route::macro('view', function ($url, $view, $name = null) {
            return Route::get($url, [
                'as' => $name,
                'uses' => function () use ($view) {
                    return view($view);
                },
            ]);
        });
    }

    private function registerWidget()
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

    private function registerJsonWidget()
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
