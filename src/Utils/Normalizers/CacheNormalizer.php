<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class CacheNormalizer
{
    /**
     * ّFigures out how long the cache life time should be.
     * @param $widget
     * @return null
     */
    public function normalizeCacheLifeTime($widget)
    {
        if ($widget->cacheLifeTime === 'env_default') {
            $widget->cacheLifeTime = (int)(env('WIDGET_DEFAULT_CACHE_LIFETIME', 0));
        }

        if ($widget->cacheLifeTime === 'forever') {
            $widget->cacheLifeTime = -1;
        }
    }

    /**
     * ّFigures out what the cache tags should be.
     * @param $widget
     * @return null
     */
    public function normalizeCacheTags($widget)
    {
        if (!$this->cacheCanUseTags() || !$widget->cacheTags) {
            return $widget->cacheTags = null;
        }

        if (is_array($widget->cacheTags)) {
            return $widget->cacheTags;
        }

        if (is_string($widget->cacheTags)) {
            return $widget->cacheTags = [$widget->cacheTags];
        }

        throw new \InvalidArgumentException('Cache Tags should be of type String or Array.');
    }

    /**
     * Determine whether cache tags should be applied or not
     * @return bool
     */
    private function cacheCanUseTags()
    {
        return !in_array(env('CACHE_DRIVER', 'file'), ['file', 'database']);
    }
}
