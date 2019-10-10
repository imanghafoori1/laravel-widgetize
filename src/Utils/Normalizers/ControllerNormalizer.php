<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Illuminate\Support\Str;
use Imanghafoori\Widgets\Utils\NormalizerContract;

class ControllerNormalizer implements NormalizerContract
{
    /**
     * Figures out which method should be called as the controller.
     * @param object $widget
     * @return void
     */
    public function normalize($widget): void
    {
        [$controllerMethod, $ctrlClass] = $this->determineDataMethod($widget);

        if ($ctrlClass !== null) {
            $this->checkControllerExists($ctrlClass);
            $this->checkDataMethodExists($controllerMethod);
        }

        $widget->controller = $controllerMethod;
    }

    /**
     * @param string $ctrlClass
     */
    private function checkControllerExists(string $ctrlClass): void
    {
        if (! class_exists($ctrlClass)) {
            throw new \InvalidArgumentException("Controller class: [{$ctrlClass}] not found.");
        }
    }

    /**
     * @param $ctrlClass
     */
    private function checkDataMethodExists($ctrlClass): void
    {
        if (is_string($ctrlClass)) {
            $ctrlClass = explode('@', $ctrlClass);
        }

        [$ctrlClass, $method] = $ctrlClass;
        if (! method_exists($ctrlClass, $method)) {
            throw new \InvalidArgumentException("'data' method not found on ".$ctrlClass);
        }
    }

    /**
     * @param object $widget
     * @return array [$controllerMethod, $ctrlClass]
     */
    private function determineDataMethod($widget): array
    {
        // We decide to call data method on the widget object by default.
        // If the user has explicitly declared controller class path on
        // widget then we decide to call data method on that instead.
        if (! property_exists($widget, 'controller')) {
            return method_exists($widget, 'data') ? [[$widget, 'data'], null] : [null, null];
        }

        if (is_string($widget->controller)) {
            if (! Str::contains($widget->controller, '@')) {
                return [$widget->controller.'@data', $widget->controller];
            }
            $widget->controller = explode('@', $widget->controller);
        } elseif (is_callable($widget->controller)) {
            return [$widget->controller, null];
        }

        $ctrlClass = $widget->controller[0];
        $controllerMethod = implode('@', $widget->controller);

        return [$controllerMethod, $ctrlClass];
    }
}
