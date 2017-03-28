<?php

namespace Imanghafoori\Widgets\Utils;


use Imanghafoori\Widgets\BaseWidget;

class Normalizer
{
    private $widget;

    public function normalizeWidgetConfig(BaseWidget $widget)
    {
        $this->widget = $widget;
        $this->normalizeControllerMethod();
        $this->normalizePresenterName();
        $this->normalizeTemplateName();
        $this->normalizeContextAs();
        $this->normalizeCacheLifeTime();
        $this->normalizeCacheTags();
    }

    /**
     * Figures out which method should be called as the controller.
     * @return null
     */
    private function normalizeControllerMethod()
    {
        // If the user has explicitly declared controller class path on the sub-class
        if ($this->widget->controller === null) {
            if (!method_exists($this->widget, 'data')) {
                throw new \BadMethodCallException("'data' method not found on " . class_basename($this->widget));
            }
            // We decide to call data method on widget object.
            $this->widget->controller = [$this->widget, 'data'];
        } else {
            // If the user has specified the controller class path
            if (!class_exists($this->widget->controller)) {
                throw new \InvalidArgumentException("Controller class: [{$this->widget->controller}] not found.");
            }

            // we decide to call data method on that.
            $this->widget->controller = ($this->widget->controller) . '@data';
        }
    }

    /**
     * Figures out which method should be called as the presenter
     * @return null
     */
    private function normalizePresenterName()
    {
        if ($this->widget->presenter === 'default') {
            $presenter = class_basename($this->widget) . 'Presenter';

            if (class_exists($presenter)) {
                $this->widget->presenter = $presenter . '@presenter';
            } else {
                $this->widget->presenter = null;
            }
        } else {
            if (class_exists($this->widget->presenter) === false) {
                throw new \InvalidArgumentException("Presenter Class [{$this->widget->presenter}] not found.");
            }
            $this->widget->presenter = $this->widget->presenter . '@present';
        }

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
        if ($this->cacheShouldBeTagged()) {
            if (is_string($this->widget->cacheTags)) {
                $this->widget->cacheTags = [$this->widget->cacheTags];
            }

            if (!is_array($this->widget->cacheTags)) {
                throw new \InvalidArgumentException('Cache Tags should be of type String or Array.');
            }
        }
    }
    /**
     * Determine whether cache tags should be applied or not
     * @return bool
     */
    private function cacheShouldBeTagged()
    {
        return !in_array(env('CACHE_DRIVER', 'file'), ['file', 'database']) and $this->widget->cacheTags;
    }

}