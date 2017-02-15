<?php


namespace Imanghafoori\Widgets;


use Illuminate\Support\Facades\Cache;

abstract class BaseWidget
{
    protected $template = null;
    protected $minifyOutput = true;
    protected $cacheLifeTime = 0;
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
    }

    private function normalizePresenterName()
    {
        if ($this->presenter === 'default') {
            $this->presenter = get_called_class() . 'Presenter';
        }
    }

    private function normalizeTemplateName()
    {
        if ($this->template === null) {
            // class name without namespace.
            $className = str_replace('App\\Widgets\\', '', get_called_class());
            // replace slashes with dots
            $className = str_replace(['\\', '/'], '.', $className);
            $this->template = 'Widgets::' . $className;
        }
    }

    private function normalizeContextAs()
    {
        // removes the $ sign.
        $this->contextAs = str_replace('$', '', $this->contextAs);
    }

    /**
     * This method is called when you try to invoke the object like a function in blade files.
     * like this : {!! $myWidgetObj('param1') !!}
     * @param array $args
     * @return
     */
    public function __invoke(...$args)
    {
        return $this->generateHtml(...$args);
    }

    private function generateHtml(...$args)
    {
        // Everything inside this function is executed only when the cache is not available.
        $expensivePhpCode = function () use ($args) {
            $this->prepareDataForView($args);
            return $this->renderTemplate(); // Then render the template with the returned data.
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
     * @return mixed
     */
    private function prepareDataForView($args)
    {
        // Here we call the data method on the widget class.
        $this->viewData = app()->call( $this->controller, $args);

        if (class_exists($this->presenter)) {
            // We make an object and call the `present` method on it.
            // Piping the data through the presenter before sending it to view.
            $this->viewData = resolve($this->presenter)->present($this->viewData);
        }
    }

    private function renderTemplate()
    {
        // Here we render the view file to raw html.
        $this->html = view($this->template, [$this->contextAs => $this->viewData])->render();

        // We try to minify the html before storing it in cache to save space.
        if (env('WIDGET_MINIFICATION', false) or app()->environment('production')) {
            $this->minifyHtml();
        }

        // We add some comments before and after the widget to be able to
        // easily identify the widget in browser's developer tool.
        if(env('WIDGET_IDENTIFIER',false)){
            $this->addIdentifierToHtml();
        }

        return $this->html;
    }

    private function minifyHtml()
    {
        $replace = [
//                '/<!--[^\[](.*?)[^\]]-->/s' => '',
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

        $this->html = "<!-- ^ --> <!--".get_called_class()."  --> <!-- '$name' Widget Start -->"
            . $this->html .
            "<!-- '$name' Widget End --> <!--   --> <!-- ~ -->";
    }

    private function makeCacheKey($arg)
    {
        return md5(json_encode($arg, JSON_FORCE_OBJECT) . $this->template . get_called_class());
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

    /**
     * @return mixed
     */
    private function normalizeControllerMethod()
    {
        // If the user has specified the class path we call data method on that.
        if ($this->controller) {
            $this->controller = ($this->controller) . '@data';
        } else {
            // otherwise we call data method on this object.
            $this->controller = [$this, 'data'];
        }
    }

    /**
     * @return bool
     */
    private function widgetShouldUseCache()
    {
        // The caching is turned off when:
        // 1- we are running tests
        // 2- have disabled it in .env fil
        // 3- have set the time to 0 minutes
         return ((env('WIDGET_CACHE', false) !== false) and (!app()->environment('testing')) and ($this->cacheLifeTime !== 0));
    }

}
