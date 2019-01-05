<?php

namespace Imanghafoori\Widgets\Utils;

class Normalizer
{
    /**
     * @var array
     */
    private $normalizers = [];

    public function addNormalizer(NormalizerContract $normalizer): void
    {
        $this->normalizers[] = $normalizer;
    }

    /**
     * Figures out and sets the widget configs according to conventions.
     *
     * @param object $widget
     */
    public function normalizeWidgetConfig($widget): void
    {
        // to avoid normalizing a widget multiple times unnecessarily :
        if (isset($widget->isNormalized)) {
            return;
        }

        foreach ($this->normalizers as $normalizer) {
            $normalizer->normalize($widget);
        }
        $widget->isNormalized = true;
    }
}
