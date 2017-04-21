<?php

namespace Imanghafoori\Widgets\Utils;

/**
 * Class DebugInfo.
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

        $tpl = str_replace('.', '\\', $tpl);

        $this->html =
            "<div title='WidgetObj : ".get_class($this->widget).".php&#013;Template : {$tpl}.blade.php".$this->cacheState()."' style='box-shadow: 0px 0px 15px 5px #00c62b inset'>".$this->html.'</div>';
    }

    /**
     * Generates a string of current cache configurations.
     * @return string
     */
    private function cacheState()
    {
        if (! $this->policies->widgetShouldUseCache()) {
            return ' &#013;Cache: is globally turned off (You should put "WIDGET_CACHE=true" in .env) ';
        }

        return " &#013;Cache : {$this->widget->cacheLifeTime} (min)' ";
    }

    private function addHtmlComments()
    {
        $this->html = "<!-- '{".get_class($this->widget)."' Widget Start -->".$this->html."<!-- '".get_class($this->widget)."' Widget End -->";
    }
}
