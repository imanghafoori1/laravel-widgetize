<?php

namespace Imanghafoori\Widgets\Utils;

use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheTagsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ContextAsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;

class Normalizer
{
    /**
     * @var array
     */
    private $normalizers = [];

    /**
     * Normalizer constructor which accepts dependencies.
     *
     * @param TemplateNormalizer $templateNormalizer
     * @param CacheNormalizer $cacheNormalizer
     * @param PresenterNormalizer $presenterNormalizer
     * @param ControllerNormalizer $controllerNormalizer
     * @param \Imanghafoori\Widgets\Utils\Normalizers\CacheTagsNormalizer $cacheTagsNormalizer
     * @param ContextAsNormalizer $contextAsNormalizer
     */
    public function __construct(
        TemplateNormalizer $templateNormalizer,
        CacheNormalizer $cacheNormalizer,
        PresenterNormalizer $presenterNormalizer,
        ControllerNormalizer $controllerNormalizer,
        CacheTagsNormalizer $cacheTagsNormalizer,
        ContextAsNormalizer $contextAsNormalizer
    ) {
        $this->normalizers[] = $presenterNormalizer;
        $this->normalizers[] = $controllerNormalizer;
        $this->normalizers[] = $templateNormalizer;
        $this->normalizers[] = $cacheNormalizer;
        $this->normalizers[] = $cacheTagsNormalizer;
        $this->normalizers[] = $contextAsNormalizer;

        $this->jsonNormalizer[] = $controllerNormalizer;
        $this->jsonNormalizer[] = $cacheNormalizer;
        $this->jsonNormalizer[] = $cacheTagsNormalizer;
    }

    /**
     * Figures out and sets the widget configs according to conventions.
     *
     * @param object $widget
     */
    public function normalizeWidgetConfig($widget): void
    {
        // to avoid normalizing a widget multiple times unnecessarily :
        if (isset($widget->isNormalized)) {
            return;
        }

        foreach ($this->normalizers as $normalizer){
            $normalizer->normalize($widget);
        }
        $widget->isNormalized = true;
    }

    /**
     * Figures out and sets json widget configs according to conventions.
     *
     * @param object $widget
     */
    public function normalizeJsonWidget($widget): void
    {
        foreach ($this->normalizers as $normalizer){
            $normalizer->normalize($widget);
        }
    }
}
