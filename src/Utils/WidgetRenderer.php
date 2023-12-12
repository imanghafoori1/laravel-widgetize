<?php

namespace Imanghafoori\Widgets\Utils;

use ErrorException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Throwable;

class WidgetRenderer
{
    use SlotRenderer;

    public $html;

    private $_viewData;

    private $_policies;

    /**
     * BaseWidget constructor.
     */
    public function __construct()
    {
        $this->_policies = resolve(Policies::class);
    }

    /**
     * @param  $widget  object|string
     * @param  array  $args
     * @return string
     */
    public function renderWidget($widget, ...$args)
    {
        if (is_string($widget)) {
            $widget = $this->makeWidgetObj($widget);
        }

        if (is_array($widget)) {
            $widget = (object) $widget;
        }

        event('widgetize.rendering_widget', [$widget]);

        resolve(Normalizer::class)->normalizeWidgetConfig($widget);

        if (app()->offsetExists('debugbar')) {
            app('widgetize.debugger')->addMessage(['widget class:' => $widget, 'args:' => $args]);
        }

        return $this->generateHtml($widget, ...$args);
    }

    /**
     * @param  $widget  object
     * @return \Illuminate\Foundation\Application|mixed
     */
    private function makeWidgetObj($widget)
    {
        if (Str::startsWith($widget, ['\\'])) {
            return resolve($widget);
        }

        $widget = app()->getNamespace().'Widgets\\'.$widget;

        return resolve($widget);
    }

    /**
     * It tries to get the html from cache if possible, otherwise generates it.
     *
     * @param  $widget  object
     * @param  array  ...$args
     * @return string
     */
    private function generateHtml($widget, ...$args)
    {
        // Everything inside this function is executed only when the cache is not available.
        $expensivePhpCode = function () use ($widget, $args) {
            $this->makeDataForView($widget, $args);

            return $this->renderTemplate($widget, ...$args);
        };

        if (! $widget->cacheView) {
            return $expensivePhpCode();
        }

        // We first try to get the output from the cache before trying to run the expensive $expensivePhpCode...
        return resolve(Cache::class)->cacheResult($args, $expensivePhpCode, $widget);
    }

    /**
     * @param  $widget  object
     * @param  $args  array
     * @return null
     */
    private function makeDataForView($widget, array $args)
    {
        $expensiveCode = function () use ($widget, $args) {
            $viewData = $this->callController($widget, $args);

            if ($widget->presenter) {
                // Pipe the data through the presenter before sending it to view.
                [$class, $method] = explode('@', $widget->presenter);
                $presenterObj = App::make($class);
                $viewData = $presenterObj->{$method}($viewData);
            }

            return $viewData;
        };

        if ($widget->cacheView) {
            $this->_viewData = $expensiveCode();
        } else {
            $this->_viewData = resolve(Cache::class)->cacheResult($args, $expensiveCode, $widget, 'dataProvider');
        }
    }

    /**
     * @param  $widget  object
     * @param  null  $args
     * @return string HTML output
     *
     * @throws \Throwable
     */
    private function renderTemplate($widget, $args = null)
    {
        // Here we render the view file to raw html.
        $data = [$widget->contextAs => $this->_viewData, 'params' => $args];

        // add slots if exists
        $this->hasSlots() && $data['slots'] = $this->getSlots();

        try {
            $this->html = view($widget->template, $data)->render();
        } catch (Throwable $t) {
            throw new ErrorException('There was some error rendering '.get_class($widget).', template file: \''.$widget->template.'\' Error: '.$t->getMessage());
        }

        // We try to minify the html before storing it in cache to save space.
        if ($this->_policies->widgetShouldBeMinified($widget)) {
            $this->html = resolve(HtmlMinifier::class)->minify($this->html);
        }

        // We add some HTML comments before and after the widget output
        // So then, we will be able to easily identify the widget in browser's developer tool.
        if ($this->_policies->widgetShouldHaveDebugInfo()) {
            $this->html = resolve(DebugInfo::class)->addIdentifierToHtml($widget, $this->html);
        }

        return $this->html;
    }

    private function callController($widget, array $args)
    {
        if (! isset($widget->controller)) {
            $viewData = [];
        } elseif (is_array($widget->controller) && is_string($widget->controller[0])) {
            $viewData = call_user_func_array($widget->controller, $args);
        } else {
            // Here we call the data method on the widget class.
            $viewData = App::call($widget->controller, ...$args);
        }

        return $viewData;
    }
}
