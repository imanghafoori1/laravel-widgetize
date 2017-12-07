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

    /**
     * @param object $widget
     * @param string $html
     * @return string
     */
    public function addIdentifierToHtml($widget, $html)
    {
        $this->widget = $widget;
        $this->html = $html;
        $this->addDebugInfo();
        $this->addHtmlComments();

        return $this->html;
    }

    /**
     * Adds debug info to html as HTML title tags.
     */
    private function addDebugInfo()
    {
        $tpl = $this->getTplPath($this->widget->template);

        $this->html = "<span title='WidgetObj : ".get_class($this->widget).".php&#013;Template : {$tpl}{$this->cacheState()}'>{$this->html}</span>";
    }

    /**
     * Generates a string of current cache configurations.
     *
     * @return string
     */
    private function cacheState()
    {
        if (! $this->policies->widgetShouldUseCache()) {
            return ' &#013;Cache: is globally turned off (You should put "enable_cache" => true in config\widgetize.php) ';
        }

        return " &#013;Cache : {$this->widget->cacheLifeTime} (min)' ";
    }

    private function addHtmlComments()
    {
        $this->html = "<!-- '{".get_class($this->widget)."' Widget Start -->".$this->html."<!-- '".get_class($this->widget)."' Widget End -->";
    }

    /**
     * @param $tpl
     * @return string
     */
    private function getTplPath($tpl)
    {
        if (str_contains($tpl, 'Widgets::')) {
            $tpl = str_replace('Widgets::', app()->getNamespace().'Widgets\\', $tpl);
        }

        return str_replace('.', '\\', $tpl).'.blade.php';
    }
}
