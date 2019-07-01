<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class CacheNormalizer implements NormalizerContract
{
    /**
     * ّFigures out how long the cache life time should be.
     *
     * @param object $widget
     * @return void
     */
    public function normalize($widget): void
    {
        if (! property_exists($widget, 'cacheLifeTime')) {
            $M = config('widgetize.default_cache_lifetime', 0);
            $widget->cacheLifeTime = $this->makeFromSeconds($M * 60);
        }

        if (! property_exists($widget, 'cacheView')) {
            $widget->cacheView = true;
        }

        if ($widget->cacheLifeTime === 0) {
            $widget->cacheLifeTime = $this->makeFromSeconds(0);
        }

        if (is_object($widget->cacheLifeTime)) {
            return;
        }

        if ($widget->cacheLifeTime === 'forever' || $widget->cacheLifeTime < 0) {
            // 1209600 seconds is 2 weeks, which is long enough !
            $widget->cacheLifeTime = $this->makeFromSeconds(1209600);
        } elseif (is_numeric($widget->cacheLifeTime)) {
            $widget->cacheLifeTime = $this->makeFromSeconds($widget->cacheLifeTime * 60);
        }
    }

    public function makeFromSeconds($s)
    {
        return new \DateInterval('PT'.(string) ceil($s).'S');
    }
}
