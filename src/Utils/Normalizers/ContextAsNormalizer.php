<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class ContextAsNormalizer
{
    /**
     * Figures out what the variable name should be in view file.
     *
     * @param object $widget
     * @return null
     */
    public function normalizeContextAs($widget)
    {
        $contextAs = 'data';
        if (property_exists($widget, 'contextAs')) {
            // removes the $ sign.
            $contextAs = str_replace('$', '', (string) $widget->contextAs);
        }
        $widget->contextAs = $contextAs;
    }
}
