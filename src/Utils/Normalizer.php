<?php

namespace Imanghafoori\Widgets\Utils;

use Imanghafoori\Widgets\BaseWidget;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;

class Normalizer
{
    private $widget;
    private $presenterNormalizer;
    private $templateNormalizer;

    /**
     * Normalizer constructor.
     * @param TemplateNormalizer $templateNormalizer
     * @param CacheNormalizer $cacheNormalizer
     * @param PresenterNormalizer $presenterNormalizer
     * @param ControllerNormalizer $controllerNormalizer
     * @internal param $widget
     */
    public function __construct(TemplateNormalizer $templateNormalizer, CacheNormalizer $cacheNormalizer, PresenterNormalizer $presenterNormalizer, ControllerNormalizer $controllerNormalizer)
    {
        $this->presenterNormalizer = $presenterNormalizer;
        $this->controllerNormalizer = $controllerNormalizer;
        $this->templateNormalizer = $templateNormalizer;
        $this->cacheNormalizer = $cacheNormalizer;
    }


    public function normalizeWidgetConfig(BaseWidget $widget)
    {
        // to avoid normalizing a widget multiple times unnecessarily :
        if (isset($widget->isNormalized)) {
            return null;
        }

        $this->widget = $widget;
        $this->controllerNormalizer->normalizeControllerMethod($widget);
        $this->presenterNormalizer->normalizePresenterName($widget);
        $this->templateNormalizer->normalizeTemplateName($widget);
        $this->cacheNormalizer->normalizeCacheLifeTime($widget);
        $this->cacheNormalizer->normalizeCacheTags($widget);
        $this->normalizeContextAs();
        $widget->isNormalized = true;
    }

    /**
     * Figures out what the variable name should be in view file.
     * @return null
     */
    private function normalizeContextAs()
    {
        // removes the $ sign.
        $this->widget->contextAs = str_replace('$', '', (string)$this->widget->contextAs);
    }
}
