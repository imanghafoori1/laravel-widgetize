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
        return config('widgetize.debug_info') && ! app()->environment('production');
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
     * @param  $widget
     * @return bool
     */
    public function widgetShouldBeMinified($widget): bool
    {
        $conf = (config('widgetize.minify_html') || app()->environment('production'));

        return $widget->minifyOutput ?? $conf;
    }
}
