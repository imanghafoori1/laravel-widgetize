<?php

namespace Imanghafoori\Widgets\Utils;

/**
 * Class DebugInfo
 * @package Imanghafoori\Widgets\Utils
 */
class DebugInfo
{
    private $widget;
    private $html;
    private $policies;

    public function __construct()
    {
        $this->policies = app(Policies::class);
    }

    public function addIdentifierToHtml($widget, $html)
    {
        $this->widget = $widget;
        $this->html = $html;
        $this->addDebugInfo();
        $this->addHtmlComments();
        return $this->html;
    }

    private function addDebugInfo()
    {
        $tpl = $this->widget->template;
        if (str_contains($this->widget->template, 'Widgets::')) {
            $tpl = str_replace('Widgets::', 'app\Widgets\\', $this->widget->template);
        }
        $this->html = "<div title='" . get_class($this->widget) . "::class || template : {$tpl}" . $this->cacheState() . "' style='box-shadow: 0px 0px 15px 5px #00c62b inset'>" . $this->html . "</div>";
    }

    /**
     * Generates a string of current cache configurations.
     * @return string
     */
    private function cacheState()
    {
        if (!$this->policies->widgetShouldUseCache()) {
            return " || cache: is globally turned off (in .env set WIDGET_CACHE=true) ";
        }
        return " || cache: {$this->widget->cacheLifeTime}(min)' ";
    }

    private function addHtmlComments()
    {
        $this->html = "<!-- '{$this->widget->friendlyName}' Widget Start -->" . $this->html . "<!-- '{$this->widget->friendlyName}' Widget End -->";
    }
}
