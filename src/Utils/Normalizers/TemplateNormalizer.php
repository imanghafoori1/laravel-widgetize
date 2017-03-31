<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class TemplateNormalizer
{
    /**
     * Figures out which template to render.
     * @param $widget
     * @return null
     */
    public function normalizeTemplateName($widget)
    {
        // class name without namespace.
        $className = str_replace('App\\Widgets\\', '', class_basename($widget));

        // replace slashes with dots
        $className = str_replace(['\\', '/'], '.', $className);

        if ($widget->template === null) {
            $widget->template = 'Widgets::' . $className . 'View';
        }

        if (!view()->exists($widget->template)) {
            throw new \InvalidArgumentException("View file [{$className}View] not found by: '" . class_basename($widget) . " '");
        }
    }
}
