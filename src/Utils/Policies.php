<?php

namespace Imanghafoori\Widgets\Utils;


class Policies
{
    /**
     * @return bool
     */
    function widgetShouldHaveDebugInfo(){

        return env('WIDGET_IDENTIFIER', true) and env('APP_ENV', 'production') === 'local';
    }

    /**
     * @return bool
     */
    function widgetShouldUseCache()
    {
        /*
         * ================================== *
         |  The caching is turned off when:   |
         |  1- we are running tests           |
         |  2- have disabled it in .env file  |
         |  3- have set the time to 0 minutes |
         * ================================== *
        */
        return ((env('WIDGET_CACHE', false) !== false) and (!app()->environment('testing')));
    }

    /**
     * @return bool
     */
    function widgetShouldBeMinified()
    {
        return env('WIDGET_MINIFICATION', false) or app()->environment('production');
    }
}