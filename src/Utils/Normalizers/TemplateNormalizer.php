<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class TemplateNormalizer
{
    /**
     * Figures out which template to render.
     * @param object $widget
     * @return null
     */
    public function normalizeTemplateName($widget)
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
