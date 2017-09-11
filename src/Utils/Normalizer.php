<?php

namespace Imanghafoori\Widgets\Utils;

use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ContextAsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;

class Normalizer
{
    private $presenterNormalizer;

    private $templateNormalizer;

    private $cacheNormalizer;

    private $controllerNormalizer;

    private $contextAsNormalizer;

    /**
     * Normalizer constructor which accepts dependencies.
     *
     * @param TemplateNormalizer $templateNormalizer
     * @param CacheNormalizer $cacheNormalizer
     * @param PresenterNormalizer $presenterNormalizer
     * @param ControllerNormalizer $controllerNormalizer
     * @param ContextAsNormalizer $contextAsNormalizer
     */
    public function __construct(
        TemplateNormalizer $templateNormalizer,
        CacheNormalizer $cacheNormalizer,
        PresenterNormalizer $presenterNormalizer,
        ControllerNormalizer $controllerNormalizer,
        ContextAsNormalizer $contextAsNormalizer
    ) {
        $this->presenterNormalizer = $presenterNormalizer;
        $this->controllerNormalizer = $controllerNormalizer;
        $this->templateNormalizer = $templateNormalizer;
        $this->cacheNormalizer = $cacheNormalizer;
        $this->contextAsNormalizer = $contextAsNormalizer;
    }

    /**
     * Figures out and sets the widget configs according to conventions.
     *
     * @param object $widget
     */
    public function normalizeWidgetConfig($widget)
    {
        // to avoid normalizing a widget multiple times unnecessarily :
        if (isset($widget->isNormalized)) {
            return;
        }

        $this->controllerNormalizer->normalizeControllerMethod($widget);
        $this->presenterNormalizer->normalizePresenterName($widget);
        $this->templateNormalizer->normalizeTemplateName($widget);
        $this->cacheNormalizer->normalizeCacheLifeTime($widget);
        $this->cacheNormalizer->normalizeCacheTags($widget);
        $this->contextAsNormalizer->normalizeContextAs($widget);
        $widget->isNormalized = true;
    }

    /**
     * Figures out and sets json widget configs according to conventions.
     *
     * @param object $widget
     */
    public function normalizeJsonWidget($widget)
    {
        $this->controllerNormalizer->normalizeControllerMethod($widget);
        $this->cacheNormalizer->normalizeCacheLifeTime($widget);
        $this->cacheNormalizer->normalizeCacheTags($widget);
    }
}
