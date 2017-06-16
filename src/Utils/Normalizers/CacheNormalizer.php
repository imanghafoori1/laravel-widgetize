<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class CacheNormalizer
{
    /**
     * ّFigures out how long the cache life time should be.
     * @param object $widget
     * @return null
     */
    public function normalizeCacheLifeTime($widget)
    {
        if (! property_exists($widget, 'cacheLifeTime')) {
            $widget->cacheLifeTime = (int) (config('widgetize.default_cache_lifetime', 0));
        }

        if ($widget->cacheLifeTime === 'forever' || $widget->cacheLifeTime < 0) {
            // 20.000 minutes is about 2 weeks which is long enough !
            $widget->cacheLifeTime = 20000;
        }
    }

    /**
     * ّFigures out what the cache tags should be.
     * @param object $widget
     * @return array
     */
    public function normalizeCacheTags($widget)
    {
        if (! property_exists($widget, 'cacheTags')) {
            return $widget->cacheTags = [];
        }

        if (is_array($widget->cacheTags)) {
            return $widget->cacheTags;
        }

        throw new \InvalidArgumentException('Cache Tags must be of type Array with String elements.');
    }
}
