<?php

namespace Imanghafoori\Widgets;

use Illuminate\Container\Container;
use Imanghafoori\Widgets\Utils\Normalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheTagsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ContextAsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;

class SingletonServices
{
    /**
     * Register classes as singletons.
     *
     * @param $app
     */
    private $singletonClasses = [
        Utils\HtmlMinifier::class,
        Utils\DebugInfo::class,
        Utils\Policies::class,
        Utils\Cache::class,
        Utils\CacheTag::class,
        Utils\WidgetRenderer::class,
    ];

    private $normalizers = [
        CacheNormalizer::class,
        TemplateNormalizer::class,
        PresenterNormalizer::class,
        ControllerNormalizer::class,
        ContextAsNormalizer::class,
        CacheTagsNormalizer::class,
    ];

    protected function declareAsSingleton(Container $app)
    {
        foreach ($this->singletonClasses as $class) {
            $app->singleton($class);
        }
    }

    public function registerSingletons(Container $app)
    {
        $app->singleton('command.imanghafoori.widget', function (Container $app) {
            return $app['Imanghafoori\Widgets\WidgetGenerator'];
        });

        $app->singleton(Normalizer::class, function () {
            $mainNormalizer = new Utils\Normalizer();
            foreach ($this->normalizers as $normalizer) {
                $mainNormalizer->addNormalizer(new $normalizer);
            }

            return $mainNormalizer;
        });

        $this->declareAsSingleton($app);
    }
}
