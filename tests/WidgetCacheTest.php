<?php

require_once 'test_stubs.php';

class WidgetCacheTest extends TestCase
{
    public function test_the_view_and_controller_are_rendered_only_once_when_cache_is_enabled()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.debug_info' => false]);
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

    public function test_avoids_caching_when_lifetime_is_set_to_zero()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';
        //assert
        Cache::shouldReceive('remember')->times(0);
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->times(5)->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->times(5)->andReturn('<p>some text</p>');
        \App::shouldReceive('call')->times(5)->andReturn('foo');
        //act
        $widget = new ZeroLifeTimeWidget();
        $result1 = render_widget($widget);
        $result2 = render_widget($widget);
        $result3 = render_widget($widget);
        $result4 = render_widget($widget);
        $result5 = render_widget($widget);

        //$this->assertEquals('<p>some text</p>', $result2);
        //$this->assertEquals('<p>some text</p>', $result5);
    }

    public function test_caches_the_result_forever_when_lifetime_is_negative()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';
        //assert
        Cache::shouldReceive('remember')->times(2)->andReturn('<p>some text</p>');
        View::shouldReceive('exists')->times(2)->andReturn(true);

        //act
        $widget = new ForeverWidget();
        $widget2 = new ForeverWidget2();
//        $widget2 = new Widget2();

        $result1 = render_widget($widget);
        $result2 = render_widget($widget2, 'sdfvsf');

        $this->assertEquals('<p>some text</p>', $result1);
        $this->assertEquals($widget->cacheLifeTime, 20000);
        $this->assertEquals($widget2->cacheLifeTime, 20000);
    }

    public function test_cacheKey_method()
    {
        putenv('CACHE_DRIVER=array');
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        config(['widgetize.debug_info' => false]);

        app()['env'] = 'production';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<p>some text</p>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        $widget = new CustomCacheKeyWidget();
        render_widget($widget);

        $this->assertTrue(cache()->has('abcde'));
        $this->assertEquals(cache()->get('abcde'), '<p>some text</p>');
    }

    public function test_the_cache_tags()
    {
        //putenv('CACHE_DRIVER=array');
        config(['cache.default'=> 'file']);
        config(['widgetize.debug_info' => false]);
        config(['widgetize.enable_cache' => true]);
        config(['widgetize.default_cache_lifetime' => 1]);
        app()['env'] = 'production';

        //assert
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->times(3)->with('hello', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->times(3)->andReturn('<p>some text</p>');
        \App::shouldReceive('call')->times(3)->andReturn('foo');

        //act
        $widget = new TaggedWidget();
        $result1 = render_widget($widget);
        $result2 = render_widget($widget);
        expire_widgets(['t1']); // causes a re-render
        $result3 = render_widget($widget);
        expire_widgets(['_foo_']); // has no effect
        $result4 = render_widget($widget);
        expire_widgets(['t2']); // causes a re-render
        $result5 = render_widget($widget);
        $result6 = render_widget($widget);

        $this->assertEquals('<p>some text</p>', $result2);
        $this->assertEquals('<p>some text</p>', $result5);
    }
}
