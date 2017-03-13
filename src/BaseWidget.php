<?php


namespace Imanghafoori\Widgets;

abstract class BaseWidget
{
    protected $template = null;
    protected $minifyOutput = true;
    protected $cacheLifeTime = 'env_default';
    protected $contextAs = '$data';
    protected $presenter = 'default';
    protected $controller = null;
    protected $cacheTags = null;
    private $html;
    private $viewData;

    /**
     * BaseWidget constructor.
     */
    public function __construct()
    {
        $this->normalizeControllerMethod();
        $this->normalizePresenterName();
        $this->normalizeTemplateName();
        $this->normalizeContextAs();
        $this->normalizeCacheLifeTime();
        $this->normalizeCacheTags();
    }

    /**
     * @return null
     */
    private function normalizeControllerMethod()
    {
        // If the user has explicitly declared controller class path on the sub-class
        if ($this->controller === null) {
            if (!method_exists($this, 'data')) {
                throw new \BadMethodCallException("'data' method not found on " . get_called_class());
            }
            // We decide to call data method on widget object.
            $this->controller = [$this, 'data'];
        } else {
            // If the user has specified the controller class path
            if (!class_exists($this->controller)) {
                throw new \InvalidArgumentException("Controller class: [{$this->controller}] not found.");
            }

            // we decide to call data method on that.
            $this->controller = ($this->controller) . '@data';
        }
    }

    /**
     * @return null
     */
    private function normalizePresenterName()
    {
        if ($this->presenter === 'default') {
            $presenter = get_called_class() . 'Presenter';

            if (class_exists($presenter)) {
                $this->presenter = $presenter.'@presenter';
            }else{
                $this->presenter = null;
            }
        } else {
            if (class_exists($this->presenter) === false) {
                throw new \InvalidArgumentException("Presenter Class [{$this->presenter}] not found.");
            }
            $this->presenter = $this->presenter.'@present';
        }

    }

    /**
     * @return null
     */
    private function normalizeTemplateName()
    {
        // class name without namespace.
        $className = str_replace('App\\Widgets\\', '', get_called_class());
        // replace slashes with dots
        $className = str_replace(['\\', '/'], '.', $className);

        if ($this->template === null) {
            $this->template = 'Widgets::' . $className . 'View';
        }

        if (!view()->exists($this->template)) {
            throw new \InvalidArgumentException("View file [{$className}View] not found by: '" . get_called_class() . " '");
        }
    }

    /**
     * @return null
     */
    private function normalizeContextAs()
    {
        // removes the $ sign.
        $this->contextAs = str_replace('$', '', (string)$this->contextAs);
    }

    /**
     * @return null
     */
    private function normalizeCacheLifeTime()
    {
        if ($this->cacheLifeTime === 'env_default') {
            $this->cacheLifeTime = (int)(env('WIDGET_DEFAULT_CACHE_LIFETIME', 0));
        };
    }

    private function normalizeCacheTags()
    {
        if ($this->cacheShouldBeTagged()) {
            if (is_string($this->cacheTags)) {
                $this->cacheTags = [$this->cacheTags];
            }

            if (!is_array($this->cacheTags)) {
                throw new \InvalidArgumentException('Cache Tags should be of type String or Array.');
            }
        }
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

        $key = $this->makeCacheKey($args);

        // We first try to get the output from the cache before trying to run the expensive $expensivePhpCode...
        if ($this->widgetShouldUseCache()) {
            return $this->cacheResult($key, $expensivePhpCode);
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
        if ($this->widgetShouldBeMinified()) {
            $this->minifyHtml();
        }

        // We add some HTML comments before and after the widget output
        // So then, we will be able to easily identify the widget in browser's developer tool.
        if (env('WIDGET_IDENTIFIER', false)) {
            $this->addIdentifierToHtml();
        }

        return $this->html;
    }

    /**
     * @return bool
     */
    private function widgetShouldBeMinified()
    {
        return env('WIDGET_MINIFICATION', false) or app()->environment('production');
    }

    /**
     * @return null
     */
    private function minifyHtml()
    {
        $replace = [
            '<!--(.*?)-->' => '', //remove comments
            "/<\?php/" => '<?php ',
            "/\n([\S])/" => '$1',
            "/\r/" => '', // remove carrage return
            "/\n/" => '', // remove new lines
            "/\t/" => '', // remove tab
            "/\s+/" => ' ', // remove spaces
        ];

        $this->html = preg_replace(array_keys($replace), array_values($replace), $this->html);

    }

    private function addIdentifierToHtml()
    {
        $name = $this->friendlyName;

        $this->html = "<!-- ^ --> <!--" . get_called_class() . "  --> <!-- '$name' Widget Start -->"
            . $this->html .
            "<!-- '$name' Widget End --> <!--   --> <!-- ~ -->";
    }

    /**
     * @param $arg
     * @return string
     */
    private function makeCacheKey($arg)
    {
        return md5(json_encode($arg, JSON_FORCE_OBJECT) . $this->template . get_called_class());
    }

    /**
     * @return bool
     */
    private function widgetShouldUseCache()
    {
        /*
         * ================================== *
         |  The caching is turned off when:   |
         |  1- we are running tests           |
         |  2- have disabled it in .env file  |
         |  3- have set the time to 0 minutes |
         * ================================== *
        */
        return ((env('WIDGET_CACHE', false) !== false) and (!app()->environment('testing')) and ($this->cacheLifeTime !== 0));
    }

    private function cacheResult($key, $phpCode)
    {
        $cache = app()->make('cache');

        if($this->cacheTags){
            $cache = $cache->tags($this->cacheTags);
        }

        if ($this->cacheLifeTime > 0) {
            return $cache->remember($key, $this->cacheLifeTime, $phpCode);
        }

        if ($this->cacheLifeTime == 'forever' or $this->cacheLifeTime < 0) {
            return $cache->rememberForever($key, $phpCode);
        }
    }

    /**
     * This method is called when you try to print the object like an string in blade files.
     * like this : {!! $myWidgetObj !!}
     */
    public function __toString()
    {
        return $this->renderWidget();
    }

    /**
     * @return bool
     */
    private function cacheShouldBeTagged()
    {
        return !in_array(env('CACHE_DRIVER','file'), ['file', 'database']) and $this->cacheTags;
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

}
