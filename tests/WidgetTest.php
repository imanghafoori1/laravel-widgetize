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

    public function test_default_view_name_is_figured_out_correctly()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('Widgets::Foo.Widget1View', ['data' => 'foo'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        \App::shouldReceive('call')->once()->andReturn('foo');

        //act
        $widget = new \App\Widgets\Foo\Widget1();
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

    public function test_data_is_passed_to_data_method_from_view()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => '222111'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        //act
        $widget = new Widget6();
        render_widget($widget, ['foo' => '111', 'bar' => '222']);
    }

    public function test_data_is_passed_to_data_method_from_view_as_string()
    {
        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()->with('hello', ['data' => '222111'], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn('<br>');
        //act
        render_widget('Foo\Widget6', ['foo' => '111', 'bar' => '222']);
    }

    public function test_json_widgets()
    {
        Response::shouldReceive('json')->once()->with('222111', 200)->andReturn('123');
        //act
        $widget = new Widget6();
        $a = json_widget($widget, ['foo' => '111', 'bar' => '222']);
        $this->assertEquals('123', $a);
    }

    public function test_json_widgets_as_string()
    {
        Response::shouldReceive('json')->once()->with('222111', 200)->andReturn('123');
        //act
        $a = json_widget('Foo\Widget6', ['foo' => '111', 'bar' => '222']);
        $this->assertEquals('123', $a);
    }
}
