<?php

namespace Imanghafoori\Widgets\Utils;


class Cache
{

    /**
     * @param $arg
     * @param $widget
     * @return string
     */
    private function makeCacheKey($arg, $widget)
    {
        return md5(json_encode($arg, JSON_FORCE_OBJECT) . $widget->template . class_basename($widget));
    }

    function cacheResult($args, $phpCode, $widget)
    {
        $key = $this->makeCacheKey($args,$widget);

        $cache = app('cache');

        if ($widget->cacheTags) {
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

}