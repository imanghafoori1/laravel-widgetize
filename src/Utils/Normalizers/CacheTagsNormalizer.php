<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class CacheTagsNormalizer implements NormalizerContract
{
    /**
     * ّFigures out what the cache tags should be.
     * @param object $widget
     * @return void
     */
    public function normalize($widget): void
    {
        if (! property_exists($widget, 'cacheTags')) {
            $widget->cacheTags = [];
        } elseif (is_array($widget->cacheTags)) {
            $this->checkTagForString($widget);
        } else {
            $this->errorOut($widget);
        }
    }

    /**
     * @param $widget
     * @param null $tag
     */
    private function errorOut($widget, $tag = null): void
    {
        $tag = $tag ?' '.$tag.'is not string' : '';
        throw new \InvalidArgumentException('Cache Tags on "'.get_class($widget).'" must be of type Array with String elements.'.$tag);
    }

    /**
     * @param $widget
     */
    protected function checkTagForString($widget): void
    {
        foreach ($widget->cacheTags as $tag) {
            if (! is_string($tag)) {
                $this->errorOut($widget);
            }
        }
    }
}
