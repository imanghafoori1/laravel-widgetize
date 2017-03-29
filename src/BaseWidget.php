<?php


namespace Imanghafoori\Widgets;

use Illuminate\Contracts\Debug\ExceptionHandler;

abstract class BaseWidget
{
    public $template = null;
    public $minifyOutput = true;
    public $cacheLifeTime = 'env_default';
    public $contextAs = '$data';
    public $presenter = 'default';
    public $controller = null;
    public $cacheTags = null;
    public $html;
    private $viewData;
    private $policies;

    /**
     * BaseWidget constructor.
     */
    public function __construct()
    {
        $this->policies = app('imanghafoori.widget.policies');
        app('imanghafoori.widget.normalizer')->normalizeWidgetConfig($this);
    }

    /**
     * This method is called when you try to invoke the object like a function in blade files.
     * like this : {!! $myWidgetObj('param1') !!}
     * @param array $args
     * @return string
     */
    public function __invoke(...$args)
    {
        return $this->renderWidget(...$args);
    }

    /**
     * @param array $args
     * @return string
     */
    private function renderWidget(...$args)
    {
        try {
            $html = $this->generateHtml(...$args);
        } catch (\Exception $e) {
            return app()->make(ExceptionHandler::class)->render(app('request'), $e)->send();
        }
        return $html;
    }

    /**
     * It tries to get the html from cache if possible, otherwise generates it.
     * @param array ...$args
     * @return string
     */
    private function generateHtml(...$args)
    {
        // Everything inside this function is executed only when the cache is not available.
        $expensivePhpCode = function () use ($args) {
            $this->prepareDataForView($args);
            // render the template with the resulting data.
            return $this->renderTemplate();
        };

        // We first try to get the output from the cache before trying to run the expensive $expensivePhpCode...
        if ($this->policies->widgetShouldUseCache($this->cacheLifeTime)) {
            return app('imanghafoori.widget.cache')->cacheResult($args, $expensivePhpCode, $this);
        }

        return $expensivePhpCode();
    }

    /**
     * @param $args
     * @return null
     */
    private function prepareDataForView($args)
    {
        // Here we call the data method on the widget class.
        $viewData = \App::call($this->controller, $args);

        if (($this->presenter)) {
            // We make an object and call the `present` method on it.
            // Piping the data through the presenter before sending it to view.
            $viewData = \App::call($this->presenter, [$viewData]);
        }

        $this->viewData = $viewData;
    }

    private function renderTemplate()
    {
        // Here we render the view file to raw html.
        $this->html = view($this->template, [$this->contextAs => $this->viewData])->render();

        // We try to minify the html before storing it in cache to save space.
        if ($this->policies->widgetShouldBeMinified()) {
            $this->html = app('imanghafoori.widget.minifier')->minify($this->html);
        }

        // We add some HTML comments before and after the widget output
        // So then, we will be able to easily identify the widget in browser's developer tool.
        if ($this->policies->widgetShouldHaveDebugInfo()) {
            app('imanghafoori.widget.debugInfo')->addIdentifierToHtml($this);
        }

        return $this->html;
    }

    /**
     * This method is called when you try to print the object like an string in blade files.
     * like this : {!! $myWidgetObj !!}
     */
    public function __toString()
    {
        return $this->renderWidget();
    }
}