<?php

namespace Imanghafoori\Widgets\Utils;


class DebugInfo
{
    private $widget;
    private $html;

    function addIdentifierToHtml($widget, $html)
    {
        $this->widget = $widget;
        $this->html = $html;
        $this->addDebugInfo();
        $this->addHtmlComments();
        return $this->html;
    }

    private function addDebugInfo()
    {
        $this->html = "<div title='" . class_basename($this->widget) . "::class || template : {$this->widget->template}" . $this->cacheState() . "' style='box-shadow: 0px 0px 15px 5px #00c62b inset'>" . $this->html . "</div>";
    }
    /**
     * Generates a string of current cache configurations.
     * @return string
     */
    private function cacheState()
    {
        return " || cache: {$this->widget->cacheLifeTime}(min)' ";
    }

    private function addHtmlComments()
    {
        $this->html = "<!-- '{$this->widget->friendlyName}' Widget Start -->" . $this->html . "<!-- '{$this->widget->friendlyName}' Widget End -->";
    }
}