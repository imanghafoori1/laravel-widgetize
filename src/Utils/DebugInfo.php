<?php

namespace Imanghafoori\Widgets\Utils;


class DebugInfo
{
    private $widget;

    function addIdentifierToHtml($widget)
    {
        $this->widget = $widget;
        $this->addDebugInfo();
        $this->addHtmlComments();
    }

    private function addDebugInfo()
    {
        $this->widget->html = "<div title='" . class_basename($this->widget) . "::class || template : {$this->widget->template}" . $this->cacheState() . "' style='box-shadow: 0px 0px 15px 5px #00c62b inset'>" . $this->widget->html . "</div>";
    }

    private function addHtmlComments()
    {
        $this->widget->html = "<!-- '{$this->widget->friendlyName}' Widget Start -->" . $this->widget->html . "<!-- '{$this->widget->friendlyName}' Widget End -->";
    }

    /**
     * Generates a string of current cache configurations.
     * @return string
     */
    private function cacheState()
    {
        if ($this->widget->widgetShouldUseCache()) {
            return " || cache: {$this->widget->cacheLifeTime}(min)' ";
        }
        return " || cache : off";
    }
}