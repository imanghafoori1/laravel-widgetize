<?php

namespace Imanghafoori\Widgets\Utils;

class Cache
{
    private $_cacheTag;

    /**
     * Cache constructor.
     */
    public function __construct()
    {
        $this->_cacheTag = app(CacheTag::class);
    }

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

        if (!empty($widget->cacheTags) && $this->cacheDriverSupportsTags()) {
            $cache = $cache->tags($widget->cacheTags);
        }

        if ($widget->cacheLifeTime === 0) {
            return $phpCode();
        }

        return $cache->remember($key, $widget->cacheLifeTime, $phpCode);
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
            return $widget->cacheKey($arg);
        }

        $_key = '';

        if (method_exists($widget, 'extraCacheKeyDependency')) {
            $_key = json_encode($widget->extraCacheKeyDependency($arg));
        }

        if (!$this->cacheDriverSupportsTags()) {
            $_key .= json_encode($this->getTagTokens($widget->cacheTags));
        }

        $_key .= json_encode($arg, JSON_FORCE_OBJECT) . app()->getLocale() . $widget->template . get_class($widget);

        return md5($_key);
    }

    /**
     * @return bool
     */
    private function cacheDriverSupportsTags()
    {
        return !in_array(config('cache.default', 'file'), ['file', 'database']);
    }

    /**
     * @param $cacheTags
     * @param $_key
     * @return string
     * @internal param $widget
     */
    private function getTagTokens($cacheTags)
    {
        return array_map(function ($tag) {
            return $this->_cacheTag->getTagToken($tag);
        }, $cacheTags);
    }

    /**
     * @param $tags
     */
    public function expireTaggedWidgets($tags)
    {
        if ($this->cacheDriverSupportsTags()) {
            return \Cache::tags($tags)->flush();
        }

        if (is_string($tags)) {
            $tags = [$tags];
        }

        foreach ($tags as $tag) {
            $this->_cacheTag->generateNewToken($tag);
        }
    }
}
