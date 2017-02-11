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
     * @param null $template
     */
    public function __construct()
    {
        $this->normalizeController();
        $this->normalizePresenter();
        $this->normalizeTemplate();
        $this->normalizeContextAs();
    }

    private function normalizePresenter()
    {
        if ($this->presenter === 'default') {
            $this->presenter = get_called_class() . 'Presenter';
        }
    }

    private function normalizeTemplate()
    {
        if ($this->template === null) {
            $className = str_replace('App\\Widgets\\', '', get_called_class()); // class name without namespace.
            $className = str_replace(['\\', '/'], '.', $className); // replace slashes with dots
            $this->template = 'Widgets::' . $className;
        }
    }

    private function normalizeContextAs()
    {
        $this->contextAs = str_replace('$', '', $this->contextAs); // removes the $ sign.
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
        $phpCode = function () use ($args) {
            $this->prepareDataForView($args);
            return $this->renderTemplate(); // Then render the template with the returned data.
        };

        $key = $this->makeCacheKey($args);

        // We first chack the cache before trying to run the expensive $phpCode...
        return $this->cacheResult($key, $phpCode);
    }

    /**
     * @param $this
     * @param $args
     * @return mixed
     */
    private function prepareDataForView($args)
    {
        $this->viewData = app()->call( $this->controller, $args); // Here we call the data method on the widget class.

        if (class_exists($this->presenter)) {
            // We make an object and call the `present` method on it.
            $this->viewData = resolve($this->presenter)->present($this->viewData);
        }
    }

    private function renderTemplate()
    {
        // Here we render the view file to raw html.
        $this->html = view($this->template, [$this->contextAs => $this->viewData])->render();

        // We may try to minify the html before storing it in cache to save space.
        if ($this->minifyOutput == true and env('WIDGET_MINIFICATION', false)) {
            $this->minifyHtml();
        }

        // We add some comments to be able to easily identify the widget in browser's developer tool.
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

    protected function addIdentifierToHtml()
    {
        $name = $this->friendlyName;

        $this->html = "<!-- ^ --> <!--  --> <!-- '$name' Widget Start -->"
            . $this->html .
            "<!-- '$name' Widget End --> <!--   --> <!-- ~ -->";
    }

    private function makeCacheKey($arg)
    {
        return md5(json_encode($arg, JSON_FORCE_OBJECT) . $this->template . get_called_class());
    }

    private function cacheResult($key, $phpCode)
    {
        // The caching is turned off when we are running tests
        if ((env('WIDGET_CACHE', false) == false) or (app()->environment('testing')) or ($this->cacheLifeTime === 0)) {
            return $phpCode();
        }

        if (env('WIDGET_CACHE', false) == true) {

            if ($this->cacheLifeTime > 0) {
                return Cache::remember($key, $this->cacheLifeTime, $phpCode);
            }

            if ($this->cacheLifeTime == 'forever' or $this->cacheLifeTime < 0) {
                return Cache::rememberForever($key, $phpCode);
            }
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

    private function normalizeController()
    {
        if ($this->controller) {
            $this->controller = ($this->controller) . '@data';
        } else {
            $this->controller = [$this, 'data'];
        }
    }

}
