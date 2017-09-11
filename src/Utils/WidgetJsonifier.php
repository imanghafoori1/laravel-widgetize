<?php

namespace Imanghafoori\Widgets\Utils;

use Illuminate\Contracts\Debug\ExceptionHandler;

class WidgetJsonifier
{
    /**
     * @param $widget object|string
     * @param array $args
     * @return string
     */
    public function jsonResponse($widget, ...$args)
    {
        if (is_string($widget)) {
            $widget = $this->makeWidgetObj($widget);
        }

        app(Normalizer::class)->normalizeJsonWidget($widget);

        try {
            $json = $this->generateJson($widget, ...$args);
        } catch (\Exception $e) {
            return app()->make(ExceptionHandler::class)->render(app('request'), $e)->send();
        }

        return $json;
    }

    /**
     * @param $widget object
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function makeWidgetObj($widget)
    {
        $widget = app()->getNamespace().'Widgets\\'.$widget;

        return app($widget);
    }

    /**
     * It tries to get the html from cache if possible, otherwise generates it.
     *
     * @param $widget object
     * @param array ...$args
     *
     * @return string
     */
    private function generateJson($widget, ...$args)
    {
        // Everything inside this function is executed only when the cache is not available.
        $expensivePhpCode = function () use ($widget, $args) {
            $data = \App::call($widget->controller, ...$args);

            // render the template with the resulting data.
            return response()->json($data, 200);
        };

        // We first try to get the output from the cache before trying to run the expensive $expensivePhpCode...
        if (app(Policies::class)->widgetShouldUseCache()) {
            return app(Cache::class)->cacheResult($args, $expensivePhpCode, $widget, 'json');
        }

        return $expensivePhpCode();
    }
}
