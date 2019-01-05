<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class CacheTagsNormalizer implements NormalizerContract
{

    /**
     * ّFigures out what the cache tags should be.
     * @param object $widget
     * @return array
     */
    public function normalize($widget)
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