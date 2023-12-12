<?php

namespace Tests;

use Illuminate\Support\Facades\View;
use Imanghafoori\Widgets\Utils\WidgetRenderer;

class SlotWidgetTest extends TestCase
{
    private $renderer;

    public function setup(): void
    {
        parent::setUp();

        $this->renderer = app(WidgetRenderer::class);
    }

    public function test_set_slot()
    {
        $renderer = $this->renderer;

        $this->assertEquals(false, $renderer->hasSlots());

        $renderer->startSlot('message');
        $renderer->renderSlot('<h1> test set slot </h1>');
        ob_end_clean();

        $this->assertEquals(true, $renderer->hasSlots());
    }

    public function test_get_then_remove_current_slots()
    {
        $slotName = 'message';
        $slotContent = '<h1> test set and clean slot </h1>';

        $renderer = $this->renderer;

        $renderer->startSlot($slotName);
        $renderer->renderSlot($slotContent);
        ob_end_clean();

        $this->assertEquals($slotContent, $renderer->getSlots()[$slotName]);
    }

    public function test_define_two_slots()
    {
        $this->test_get_then_remove_current_slots();

        $slotName = 'links';
        $slotContent = "<a href='#'>blog</a>";

        $renderer = $this->renderer;

        $renderer->startSlot($slotName);
        $renderer->renderSlot($slotContent);
        ob_end_clean();

        $this->assertEquals($slotContent, $renderer->getSlots()[$slotName]);
    }

    public function test_widget_with_a_slot()
    {
        $renderer = $this->renderer;

        $slotName = 'header_links';
        $slotContent = "<a href='#'> login </a>";

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()
            ->with('hello', ['data' => 'bbaa', 'params' => ['arg1' => 'a', 'arg2' => 'bb'], 'slots' => [$slotName => $slotContent]], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn($slotContent);

        $renderer->startSlot($slotName);
        $renderer->renderSlot($slotContent);
        ob_end_clean();

        //act
        $widget = new Widget4Slot();
        $widget->controller = 'Tests\Widget764Ctrl@meta';
        $renderer->renderWidget($widget, ['arg1' => 'a', 'arg2' => 'bb']);
    }

    public function test_widget_with_many_slots()
    {
        $renderer = $this->renderer;

        $slots = [
            'header_links' => "<a href='#'> login </a>",
            'message' => '<h1> this is my message </h1>',
            'footer_links' => "<a href='#'> about us </a>",
        ];

        View::shouldReceive('exists')->once()->andReturn(true);
        View::shouldReceive('make')->once()
            ->with('hello', ['data' => 'bbaa', 'params' => ['arg1' => 'a', 'arg2' => 'bb'], 'slots' => $slots], [])->andReturn(app('view'));
        View::shouldReceive('render')->once()->andReturn(implode(',', array_values($slots)));

        foreach ($slots as $name => $content) {
            $renderer->startSlot($name);
            $renderer->renderSlot($content);
            ob_end_clean();
        }

        //act
        $widget = new Widget4Slot();
        $widget->controller = 'Tests\Widget764Ctrl@meta';
        $renderer->renderWidget($widget, ['arg1' => 'a', 'arg2' => 'bb']);
    }
}

class Widget4Slot
{
    public $template = 'hello';
    public $controller = 'Widget764Ctrl';
}

class Widget764Ctrl
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
