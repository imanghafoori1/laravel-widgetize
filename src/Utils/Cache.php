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
     * @param array $args
     * @param callable $phpCode
     * @param object $widgetObj
     *
     * @param string $form
     * @return null
     */
    public function cacheResult(array $args, callable $phpCode, $widgetObj, $form = 'HTML')
    {
        $key = $this->makeCacheKey($args, $widgetObj, $form);

        $cache = app('cache');

        if (! empty($widgetObj->cacheTags) && $this->cacheDriverSupportsTags()) {
            $cache = $cache->tags($widgetObj->cacheTags);
        }

        if ($widgetObj->cacheLifeTime === 0) {
            return $phpCode();
        }

        return $cache->remember($key, $widgetObj->cacheLifeTime, $phpCode);
    }

    /**
     * Creates a unique cache key for each possible output.
     *
     * @param array $arg
     * @param object $widget
     * @param string $form
     * @return string An MD5 string
     */
    private function makeCacheKey(array $arg, $widget, $form)
    {
        if (method_exists($widget, 'cacheKey')) {
            return $widget->cacheKey($arg);
        }

        $_key = '';

        if (method_exists($widget, 'extraCacheKeyDependency')) {
            $_key = json_encode($widget->extraCacheKeyDependency($arg));
        }

        if (! $this->cacheDriverSupportsTags()) {
            $_key .= json_encode($this->getTagTokens($widget->cacheTags));
        }

        $_key .= json_encode($arg, JSON_FORCE_OBJECT).app()->getLocale().$form.$widget->template.get_class($widget);

        return md5($_key);
    }

    /**
     * Determines cacheTagging is supported by the chosen laravel cache driver or not.
     * @return bool
     */
    private function cacheDriverSupportsTags()
    {
        return ! in_array(config('cache.default', 'file'), ['file', 'database']);
    }

    /**
     * @param string[] $cacheTags
     * @return string[]
     */
    private function getTagTokens(array $cacheTags)
    {
        return array_map(function ($tag) {
            return $this->_cacheTag->getTagToken($tag);
        }, $cacheTags);
    }

    /**
     * @param string[] $tags
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
