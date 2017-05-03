<?php

require_once 'test_stubs.php';

class WidgetTest extends TestCase
{
    public function test_presenter_method_is_called_with_data_from_controller()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'barfoo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget5();
        render_widget($widget);
    }

    public function test_the_view_and_controller_are_rendered_only_once_when_cache_is_enabled()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';
        //assert
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<p>some text</p>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget1();
        $result1 = render_widget($widget);
        $result2 = render_widget($widget);
        $result3 = render_widget($widget);
        $result4 = render_widget($widget);
        $result5 = render_widget($widget);

        $this->assertEquals('<p>some text</p>', $result2);
        $this->assertEquals('<p>some text</p>', $result5);
        Cache::flush();
    }

    public function test_caches_the_result_of_controller_method_and_views()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';
        //assert
        Cache::shouldReceive('remember')->times(5)->andReturn('<p>some text</p>');
        View::shouldReceive('exists')->once()->andReturn(true);

        //act
        $widget = new Widget1();
        $result1 = render_widget($widget);
        $result2 = render_widget($widget);
        $result3 = render_widget($widget);
        $result4 = render_widget($widget);
        $result5 = render_widget($widget);

        $this->assertEquals('<p>some text</p>', $result2);
        $this->assertEquals('<p>some text</p>', $result5);
    }

    public function test_caches_the_result_forever_when_lifetime_is_negative()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';
        //assert
        Cache::shouldReceive('rememberForever')->times(2)->andReturn('<p>some text</p>');
        View::shouldReceive('exists')->times(2)->andReturn(true);

        //act
        $widget = new ForeverWidget();
        $widget2 = new ForeverWidget2();
//        $widget2 = new Widget2();

        $result1 = render_widget($widget);
        $result2 = render_widget($widget2, 'sdfvsf');

        $this->assertEquals('<p>some text</p>', $result1);
        $this->assertEquals($widget->cacheLifeTime, -1);
        $this->assertEquals($widget2->cacheLifeTime, -1);
    }

    public function test_default_view_name_is_figured_out_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('Widgets::Widget2View', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget2();
        render_widget($widget);
    }

    public function test_context_as_is_passes_to_view_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['myData' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget3();
        render_widget($widget);
    }

    public function test_controller_method_is_called_on_some_other_class()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'aaabbb'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget4();
        render_widget($widget, ['arg1' => 'aaa', 'arg2' => 'bbb']);
    }

    public function test_minifies_the_output()
    {
        config(['widgetize.minify_html' => true]);
        app()['env'] = 'local';

        $widgetOutput = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($widgetOutput);

        //act
        $widget = new Widget7();
        $widgetOutput = render_widget($widget);
        $this->assertEquals($minified, $widgetOutput);
    }

    public function test_minifies_the_output_in_production()
    {
        config(['widgetize.minify_html' => true]);
        app()['env'] = 'production';

        $html = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($html);

        //act
        $widget = new Widget7();
        $html = render_widget($widget);
        $this->assertEquals($minified, $html);
    }

    public function test_data_is_passed_to_data_method()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => '222111'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        //act
        $widget = new Widget6();
        render_widget($widget, ['foo' => '111', 'bar' => '222']);
    }
}
