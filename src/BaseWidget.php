<?php


namespace Imanghafoori\Widgets;


use Illuminate\Support\Facades\Cache;

abstract class BaseWidget
{
    protected $template = null;
    protected $minifyOutput = true;
    protected $cacheLifeTime = 'env_default';
    protected $contextAs = '$data';
    protected $presenter = 'default';
    protected $controller = null;
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
                $this->presenter = $presenter;
            }
        } else {
            if (class_exists($this->presenter) === false) {
                throw new \InvalidArgumentException("Presenter Class [{$this->presenter}] not found.");
            }
        }

    }

    /**
     * @return null
     */
    private function normalizeTemplateName()
    {
        if ($this->template === null) {
            // class name without namespace.
            $className = str_replace('App\\Widgets\\', '', get_called_class());
            // replace slashes with dots
            $className = str_replace(['\\', '/'], '.', $className);
            $this->template = 'Widgets::' . $className. 'View';
        }

        if (!view()->exists($this->template)) {
            throw new \InvalidArgumentException("View file [{$className}View] not found by: '". get_called_class()." '");
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

    /**
     * This method is called when you try to invoke the object like a function in blade files.
     * like this : {!! $myWidgetObj('param1') !!}
     * @param array $args
     * @return string
     */
    public function __invoke(...$args)
    {
        return $this->generateHtml(...$args);
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
        $viewData = app()->call($this->controller, $args);

        if (class_exists($this->presenter)) {
            // We make an object and call the `present` method on it.
            // Piping the data through the presenter before sending it to view.
            $viewData = resolve($this->presenter)->present($viewData);
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
         |  2- have disabled it in .env fil   |
         |  3- have set the time to 0 minutes |
         * ================================== *
        */
        return ((env('WIDGET_CACHE', false) !== false) and (!app()->environment('testing')) and ($this->cacheLifeTime !== 0));
    }

    private function cacheResult($key, $phpCode)
    {
        if ($this->cacheLifeTime > 0) {
            return Cache::remember($key, $this->cacheLifeTime, $phpCode);
        }

        if ($this->cacheLifeTime == 'forever' or $this->cacheLifeTime < 0) {
            return Cache::rememberForever($key, $phpCode);
        }
    }

    /**
     * This method is called when you try to print the object like an string in blade files.
     * like this : {!! $myWidgetObj !!}
     */
    public function __toString()
    {
        return $this->generateHtml();
    }

}
