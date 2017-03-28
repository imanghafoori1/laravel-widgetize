<?php

namespace Imanghafoori\Widgets\Utils;


class Cache
{
    function cacheResult($key, $phpCode, $cacheLifeTime, $cacheTags)
    {
        $cache = app('cache');

        if ($cacheTags) {
            $cache = $cache->tags($cacheTags);
        }

        if ($cacheLifeTime > 0) {
            return $cache->remember($key, $cacheLifeTime, $phpCode);
        }

        if ($cacheLifeTime < 0) {
            return $cache->rememberForever($key, $phpCode);
        }

        if ($cacheLifeTime === 0) {
            return $phpCode();
        }
    }

}