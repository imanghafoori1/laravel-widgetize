<?php

namespace Imanghafoori\Widgets\Utils;


use Imanghafoori\Widgets\BaseWidget;

class Normalizer
{
    private $widget;

    public function normalizeWidgetConfig(BaseWidget $widget)
    {
        // to avoid normalizing a widget multiple times unnecessarily :
        if(isset($widget->isNormalized)){
            return null;
        }

        $this->widget = $widget;
        $this->normalizeControllerMethod();
        $this->normalizePresenterName();
        $this->normalizeTemplateName();
        $this->normalizeContextAs();
        $this->normalizeCacheLifeTime();
        $this->normalizeCacheTags();
        $widget->isNormalized = true;
    }

    /**
     * Figures out which method should be called as the controller.
     * @return null
     */
    private function normalizeControllerMethod()
    {
        // We decide to call data method on widget object by default.
        $controllerMethod = [$this->widget, 'data'];
        $ctrlClass = get_class($this->widget);

        // If the user has explicitly declared controller class path on widget
        // then we decide to call data method on that instead.
        if ($this->widget->controller) {
            $ctrlClass = $this->widget->controller;
            $controllerMethod = ($this->widget->controller) . '@data';
        }

        if (!method_exists($ctrlClass, 'data')) {
            throw new \BadMethodCallException("'data' method not found on " . class_basename($this->widget));
        }

        if (!class_exists($ctrlClass)) {
            throw new \InvalidArgumentException("Controller class: [{$ctrlClass}] not found.");
        }

        $this->widget->controller = $controllerMethod;
    }

    /**
     * Figures out which method should be called as the presenter
     * @return null
     */
    private function normalizePresenterName()
    {
        $presenter = class_basename($this->widget) . 'Presenter';

        $method = null;
        if (class_exists($presenter)) {
            $method = $presenter . '@presenter';
        }

        if ($this->widget->presenter !== 'default') {
            if (!class_exists($this->widget->presenter)) {
                throw new \InvalidArgumentException("Presenter Class [{$this->widget->presenter}] not found.");
            }
            $method = $this->widget->presenter . '@present';
        }

        $this->widget->presenter = $method;

    }

    /**
     * Figures out which template to render.
     * @return null
     */
    private function normalizeTemplateName()
    {
        // class name without namespace.
        $className = str_replace('App\\Widgets\\', '', class_basename($this->widget));
        // replace slashes with dots
        $className = str_replace(['\\', '/'], '.', $className);

        if ($this->widget->template === null) {
            $this->widget->template = 'Widgets::' . $className . 'View';
        }

        if (!view()->exists($this->widget->template)) {
            throw new \InvalidArgumentException("View file [{$className}View] not found by: '" . class_basename($this->widget) . " '");
        }
    }

    /**
     * Figures out what the variable name should be in view file.
     * @return null
     */
    private function normalizeContextAs()
    {
        // removes the $ sign.
        $this->widget->contextAs = str_replace('$', '', (string)$this->widget->contextAs);
    }

    /**
     * ّFigures out how long the cache life time should be.
     * @return null
     */
    private function normalizeCacheLifeTime()
    {
        if ($this->widget->cacheLifeTime === 'env_default') {
            $this->widget->cacheLifeTime = (int)(env('WIDGET_DEFAULT_CACHE_LIFETIME', 0));
        }

        if ($this->widget->cacheLifeTime === 'forever') {
            $this->widget->cacheLifeTime = -1;
        }

    }

    /**
     * ّFigures out what the cache tags should be.
     * @return null
     */
    private function normalizeCacheTags()
    {
        if (!$this->cacheShouldBeTagged()) {
            return null;
        }

        if (is_string($this->widget->cacheTags)) {
            $this->widget->cacheTags = [$this->widget->cacheTags];
        }

        if (!is_array($this->widget->cacheTags)) {
            throw new \InvalidArgumentException('Cache Tags should be of type String or Array.');
        }
    }
    /**
     * Determine whether cache tags should be applied or not
     * @return bool
     */
    private function cacheShouldBeTagged()
    {
        return !in_array(env('CACHE_DRIVER', 'file'), ['file', 'database']) && $this->widget->cacheTags;
    }

}