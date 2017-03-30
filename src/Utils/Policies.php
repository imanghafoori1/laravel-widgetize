<?php

namespace Imanghafoori\Widgets\Utils;

class Policies
{
    /**
     * Detects the widget Should Have Debug Info
     *
     * @return bool
     */
    public function widgetShouldHaveDebugInfo()
    {
        return env('WIDGET_IDENTIFIER', true) && env('APP_ENV', 'production') === 'local';
    }

    /**
     * The caching is turned off when:
     * 1- we are running tests
     * 2- have disabled it in .env file
     *
     * @return bool
     */
    public function widgetShouldUseCache()
    {
        return ((env('WIDGET_CACHE', false) !== false) && (!app()->environment('testing')));
    }

    /**
     * Widget Should Be Minified or Not
     *
     * @return bool
     */
    public function widgetShouldBeMinified()
    {
        return env('WIDGET_MINIFICATION', false) || app()->environment('production');
    }
}
