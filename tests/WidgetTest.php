<?php

class Widget1 extends Imanghafoori\Widgets\BaseWidget
{
    protected $template = 'hello';

    public function data()
    {
    }
}

class Widget2 extends Imanghafoori\Widgets\BaseWidget
{
    public function data()
    {
    }
}

class Widget3 extends Imanghafoori\Widgets\BaseWidget
{
    protected $template = 'hello';
    protected $contextAs = '$myData';

    public function data()
    {
    }
}

class Widget4 extends Imanghafoori\Widgets\BaseWidget
{
    protected $template = 'hello';
    protected $controller = 'Widget4Ctrl';

    public function data()
    {
    }
}

class Widget4Ctrl
{
    public function data()
    {
    }
}


class Widget5 extends Imanghafoori\Widgets\BaseWidget
{
    protected $template = 'hello';
    protected $presenter = 'Widget5Presenter';

    public function data()
    {
        return 'foo';
    }
}

class Widget5Presenter
{
    public function present($data)
    {
        return 'bar' . $data;
    }
}


class WidgetTest extends TestCase
{
    public function test_presenter_method_is_called_with_data_from_controller()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'barfoo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget5($cache);
        (string)$widget;

    }

    public function test_the_view_and_controller_are_rendered_only_once_when_cache_is_enabled()
    {
        putenv('CACHE_DRIVER=array');
        putenv('WIDGET_CACHE=true');
        putenv('WIDGET_DEFAULT_CACHE_LIFETIME=1');
        app()['env'] = 'production';
        //assert
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<p>some text</p>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget1();
        $result1 = (string)$widget;
        $result2 = (string)$widget;
        $result3 = (string)$widget;
        $result4 = (string)$widget;
        $result5 = (string)$widget;

        $this->assertEquals('<p>some text</p>', $result2);
        $this->assertEquals('<p>some text</p>', $result5);
        Cache::flush();
    }

    public function test_caches_the_result_of_controller_method_and_views()
    {
        putenv('CACHE_DRIVER=array');
        putenv('WIDGET_CACHE=true');
        putenv('WIDGET_DEFAULT_CACHE_LIFETIME=1');
        app()['env'] = 'production';
        //assert
        Cache::shouldReceive('remember')->times(5)->andReturn('<p>some text</p>');
        View::shouldReceive('exists')->once()->andReturn(true);

        //act
        $widget = new Widget1();
        $result1 = (string)$widget;
        $result2 = (string)$widget;
        $result3 = (string)$widget;
        $result4 = (string)$widget;
        $result5 = (string)$widget;

        $this->assertEquals('<p>some text</p>', $result2);
        $this->assertEquals('<p>some text</p>', $result5);
    }

    public function test_default_view_name_is_figured_out_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('Widgets::Widget2View', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');


        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget2($cache);
        (string)$widget;
    }

    public function test_context_as_is_passes_to_view_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['myData' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget3($cache);
        (string)$widget;

    }

    public function test_controller_method_is_called_on_some_other_class()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->with('Widget4Ctrl@data', [])->once()->andReturn('foo');

        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget4($cache);
        (string)$widget;

    }

    public function test_minifies_the_output()
    {
        putenv('WIDGET_MINIFICATION=true');
        app()['env'] = 'local';

        $html = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($html);
        \App::shouldReceive('call')->with('Widget4Ctrl@data', [])->once()->andReturn('foo');

        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget4($cache);
        $html = (string)$widget;
        $this->assertEquals($minified, $html);
    }

    public function test_minifies_the_output_in_production()
    {
        putenv('WIDGET_MINIFICATION=false');
        app()['env'] = 'production';

        $html = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($html);
        \App::shouldReceive('call')->with('Widget4Ctrl@data', [])->once()->andReturn('foo');

        $cache = new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore);
        //act
        $widget = new Widget4($cache);
        $html = (string)$widget;
        $this->assertEquals($minified, $html);
    }

}