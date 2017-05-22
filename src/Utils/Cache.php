<?php

namespace Imanghafoori\Widgets\Utils;

class Cache
{
    /**
     * Caches the widget output.
     *
     * @param $args
     * @param $phpCode
     * @param $widget
     *
     * @return null
     */
    public function cacheResult($args, $phpCode, $widget)
    {
        $key = $this->_makeCacheKey($args, $widget);

        $cache = app('cache');

        if (is_array($widget->cacheTags)) {
            $cache = $cache->tags($widget->cacheTags);
        }

        if ($widget->cacheLifeTime > 0) {
            return $cache->remember($key, $widget->cacheLifeTime, $phpCode);
        }

        if ($widget->cacheLifeTime < 0) {
            return $cache->rememberForever($key, $phpCode);
        }

        if ($widget->cacheLifeTime === 0) {
            return $phpCode();
        }
    }

    /**
     * Creates a unique cache key for each possible output.
     *
     * @param $arg
     * @param $widget
     *
     * @return string
     */
    private function _makeCacheKey($arg, $widget)
    {
        if (method_exists($widget, 'cacheKey')) {
            return $widget->cacheKey();
        }

        $_key = '';

        if (method_exists($widget, 'extraCacheKeyDependency')) {
            $_key = json_encode($widget->extraCacheKeyDependency());
        }

        $_key .= json_encode($arg, JSON_FORCE_OBJECT) . app()->getLocale() . $widget->template . get_class($widget);

        return md5($_key);
    }
}
