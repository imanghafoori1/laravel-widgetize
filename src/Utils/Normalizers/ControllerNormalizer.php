<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class ControllerNormalizer
{
    /**
     * Figures out which method should be called as the controller.
     * @param object $widget
     * @return void
     */
    public function normalizeControllerMethod($widget)
    {
        list($controllerMethod, $ctrlClass) = $this->determineDataMethod($widget);

        $this->checkControllerExists($ctrlClass);
        $this->checkDataMethodExists($ctrlClass);

        $widget->controller = $controllerMethod;
    }

    /**
     * @param string $ctrlClass
     */
    private function checkControllerExists($ctrlClass)
    {
        if (! class_exists($ctrlClass)) {
            throw new \InvalidArgumentException("Controller class: [{$ctrlClass}] not found.");
        }
    }

    /**
     * @param $ctrlClass
     */
    private function checkDataMethodExists($ctrlClass)
    {
        if (! method_exists($ctrlClass, 'data')) {
            throw new \InvalidArgumentException("'data' method not found on ".$ctrlClass);
        }
    }

    /**
     * @param object $widget
     * @return array [$controllerMethod, $ctrlClass]
     */
    private function determineDataMethod($widget)
    {
        // We decide to call data method on widget object by default.
        $controllerMethod = [$widget, 'data'];
        $ctrlClass = get_class($widget);

        // If the user has explicitly declared controller class path on widget
        // then we decide to call data method on that instead.
        if (property_exists($widget, 'controller')) {
            $ctrlClass = $widget->controller;
            $controllerMethod = ($ctrlClass).'@data';
        }

        return [$controllerMethod, $ctrlClass];
    }
}
