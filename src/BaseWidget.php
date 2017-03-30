<?php


namespace Imanghafoori\Widgets;

abstract class BaseWidget
{
    public $template = null;
    public $minifyOutput = true;
    public $cacheLifeTime = 'env_default';
    public $contextAs = '$data';
    public $presenter = 'default';
    public $controller = null;
    public $cacheTags = null;

    /**
     * This method is called when you try to invoke the object like a function in blade files.
     * like this : {!! $myWidgetObj('param1') !!}
     * @param array $args
     * @return string
     */
    public function __invoke(...$args)
    {
        return $this->render(...$args);
    }

    /**
     * @param array ...$args
     * @return string
     */
    public function render(...$args)
    {
        return app('imanghafoori.widget.renderer')->renderWidget($this, ...$args);
    }

    /**
     * This method is called when you try to print the object like an string in blade files.
     * like this : {!! $myWidgetObj !!}
     */
    public function __toString()
    {
        return $this->render();
    }
}
