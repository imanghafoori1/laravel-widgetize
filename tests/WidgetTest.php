<?php

namespace Tests;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class WidgetTest extends TestCase
{
    public function test_presenter_method_is_called_with_data_from_controller()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'barfoo', 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget5();
        render_widget($widget);
    }

    public function test_default_view_name_is_figured_out_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('Widgets::Tests.Widget1View', ['data' => 'foo', 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget1();
        render_widget($widget);
    }

    public function test_context_as_is_passes_to_view_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['myData' => 'foo', 'params' => null], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new Widget3();
        render_widget($widget);
    }

    public function test_controller_method_is_called_on_some_other_class()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'aaabbb', 'params' => ['arg1' => 'aaa', 'arg2' => 'bbb']], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget4();
        render_widget($widget, ['arg1' => 'aaa', 'arg2' => 'bbb']);
    }

    public function test_controller_method_is_called_on_some_other_class_2()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'bbaa', 'params' => ['arg1' => 'a', 'arg2' => 'bb']], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget4();
        $widget->controller = 'Tests\Widget4Ctrl@meta';
        render_widget($widget, ['arg1' => 'a', 'arg2' => 'bb']);
    }

    public function test_controller_method_is_called_on_some_other_class_3()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => 'bbaa', 'params' => ['arg1' => 'a', 'arg2' => 'bb']], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');

        //act
        $widget = new Widget4();
        $widget->controller = [new Widget4Ctrl, 'meta'];
        render_widget($widget, ['arg1' => 'a', 'arg2' => 'bb']);
    }

    public function test_data_is_passed_to_data_method_from_view()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => '222111', 'params' => ['foo' => '111', 'bar' => '222']], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        //act
        $widget = new Widget6();
        render_widget($widget, ['foo' => '111', 'bar' => '222']);
    }

    public function test_data_is_passed_to_data_method_from_view_as_string()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => '222111', 'params' => ['foo' => '111', 'bar' => '222']], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        //act
        render_widget('\Tests\Widget6', ['foo' => '111', 'bar' => '222']);
    }

    public function test_json_widgets()
    {
        Response::shouldReceive('json')->once()->with('222111', 200)->andReturn('123');
        View::shouldReceive('exists')->once()->andReturn(true);
        //act
        $widget = new Widget6();
        $a = json_widget($widget, ['foo' => '111', 'bar' => '222']);
        $this->assertEquals('123', $a);
    }

    public function test_json_widgets_as_string()
    {
        Response::shouldReceive('json')->once()->with('222111', 200)->andReturn('123');
        View::shouldReceive('exists')->once()->andReturn(true);
        //act
        $a = json_widget('\Tests\Widget6', ['foo' => '111', 'bar' => '222']);
        $this->assertEquals('123', $a);
    }
}

class Widget6
{
    public $template = 'hello';

    public function data($foo, $bar)
    {
        return $bar.$foo;
    }
}

class Widget5
{
    public $template = 'hello';
    public $presenter = 'Tests\Widget5Presenter';

    public function data()
    {
        return 'foo';
    }
}

class Widget4
{
    public $template = 'hello';
    public $controller = 'Tests\Widget4Ctrl';
}

class Widget13
{
    public $template = 'hello';

    public function data()
    {
    }
}

class Widget66
{
    public $template = 'hello';

    public function data($foo, $bar)
    {
        return $bar.$foo;
    }
}

class Widget4Ctrl
{
    public function data($arg1, $arg2)
    {
        return $arg1.$arg2;
    }

    public function meta($arg1, $arg2)
    {
        return $arg2.$arg1.$arg1;
    }
}

class Widget3
{
    public $template = 'hello';
    public $contextAs = '$myData';

    public function data()
    {
    }
}

class Widget1
{
    public function data()
    {
    }
}

class Widget5Presenter
{
    public function present($data)
    {
        return 'bar'.$data;
    }
}
