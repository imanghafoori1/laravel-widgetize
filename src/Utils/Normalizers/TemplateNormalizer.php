<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class TemplateNormalizer implements NormalizerContract
{
    /**
     * Figures out which template to render.
     *
     * @param object $widget
     * @return void
     */
    public function normalize($widget) : void
    {
        // class name without namespace.
        $className = str_replace('App\\Widgets\\', '', get_class($widget));

        // replace slashes with dots
        $className = str_replace(['\\', '/'], '.', $className);

        if (! property_exists($widget, 'template')) {
            $widget->template = 'Widgets::'.$className.'View';
        }

        if (! view()->exists($widget->template)) {
            throw new \InvalidArgumentException("View file \"{$widget->template}\" not found by: '".get_class($widget)." '");
        }
    }
}
