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
            $M = (int) (config('widgetize.default_cache_lifetime', 0));
            $widget->cacheLifeTime = new \DateInterval('PT'.$M.'M');
        }

        if ($widget->cacheLifeTime === 'forever' || $widget->cacheLifeTime < 0) {
            // 2 weeks which is long enough !
            $widget->cacheLifeTime = new \DateInterval('P2W');
        } elseif (is_numeric($widget->cacheLifeTime)) {
            $widget->cacheLifeTime = new \DateInterval('PT'.(string)$widget->cacheLifeTime.'M');
        }
    }
}
