<?php

namespace Imanghafoori\Widgets\Utils;

class Policies
{
    /**
     * Detects the widget Should Have Debug Info.
     *
     * @return bool
     */
    public function widgetShouldHaveDebugInfo(): bool
    {
        return config('widgetize.debug_info') && env('APP_ENV', 'production') !== 'production';
    }

    /**
     * The caching is turned off when:
     * 1 - we are running tests
     * 2 - have disabled it in config file.
     *
     * @return bool
     */
    public function widgetShouldUseCache(): bool
    {
        return config('widgetize.enable_cache') && (! app()->environment('testing'));
    }

    /**
     * Widget Should Be Minified or Not.
     *
     * @return bool
     */
    public function widgetShouldBeMinified(): bool
    {
        return config('widgetize.minify_html') || app()->environment('production');
    }
}
