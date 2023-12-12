<?php

namespace Tests;

use Illuminate\Support\Facades\View;


class WidgetMinificationTest extends TestCase
{
    public function test_minifies_the_output()
    {
        config(['widgetize.minify_html' => true]);
        config(['widgetize.debug_info' => false]);

        app()['env'] = 'local';

        $widgetOutput = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null, 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($widgetOutput);

        //act
        $widget = new Widget7();
        $widgetOutput = render_widget($widget);
        $this->assertEquals($minified, $widgetOutput);
    }

    public function test_minifies_the_output_override()
    {
        config(['widgetize.minify_html' => true]);
        config(['widgetize.debug_info' => false]);

        app()['env'] = 'local';

        $widgetOutput = '  <p>        </p>  ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null, 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($widgetOutput);

        //act
        $widget = new Widget7();
        $widget->minifyOutput = false;
        $this->assertEquals($widgetOutput, render_widget($widget));
    }

    public function test_minifies_the_output_()
    {
        config(['widgetize.minify_html' => false]);
        config(['widgetize.debug_info' => false]);

        app()['env'] = 'local';

        $widgetOutput = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null, 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($widgetOutput);

        //act
        $widget = new Widget7();
        $widget->minifyOutput = true;
        $widgetOutput = render_widget($widget);
        $this->assertEquals($minified, $widgetOutput);
    }

    public function test_minifies_the_output_in_production_with_cache_turned_off()
    {
        config(['widgetize.minify_html' => false]);
        config(['widgetize.debug_info' => false]);
        app()['env'] = 'production';

        $html = '  <p>        </p>  ';
        $minified = ' <p> </p> ';

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => null, 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($html);

        //act
        $widget = new Widget7();
        $html = render_widget($widget);
        $this->assertEquals($minified, $html);
    }
}


class Widget7
{
    public $template = 'hello';

    public function data()
    {
    }
}