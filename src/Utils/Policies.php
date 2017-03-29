<?php

namespace Imanghafoori\Widgets\Utils;


class Policies
{
    /**
     * @return bool
     */
    public function widgetShouldHaveDebugInfo(){

        return env('WIDGET_IDENTIFIER', true) && env('APP_ENV', 'production') === 'local';
    }

    /**
     * @return bool
     */
    public function widgetShouldUseCache()
    {
        /*
         * ================================== *
         |  The caching is turned off when:   |
         |  1- we are running tests           |
         |  2- have disabled it in .env file  |
         |  3- have set the time to 0 minutes |
         * ================================== *
        */
        return ((env('WIDGET_CACHE', false) !== false) && (!app()->environment('testing')));
    }

    /**
     * @return bool
     */
    public function widgetShouldBeMinified()
    {
        return env('WIDGET_MINIFICATION', false) || app()->environment('production');
    }
}