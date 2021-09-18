<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class ContextAsNormalizer implements NormalizerContract
{
    /**
     * Figures out what the variable name should be in view file.
     *
     * @param  object  $widget
     * @return void
     */
    public function normalize($widget): void
    {
        $contextAs = 'data';
        if (property_exists($widget, 'contextAs')) {
            // removes the $ sign.
            $contextAs = str_replace('$', '', (string) $widget->contextAs);
        }
        $widget->contextAs = $contextAs;
    }
}
