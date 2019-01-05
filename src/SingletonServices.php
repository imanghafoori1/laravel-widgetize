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

    protected function declareAsSingleton(Container $app)
    {
        foreach ($this->singletonClasses as $class) {
            $app->singleton($class, function () use ($class) {
                return new $class;
            });
        }
    }

    public function registerSingletons(Container $app)
    {
        $app->singleton('command.imanghafoori.widget', function (Container $app) {
            return $app['Imanghafoori\Widgets\WidgetGenerator'];
        });

        $app->singleton(Normalizer::class, function () {
            $normalizer = new Utils\Normalizer();
            $normalizer->addNormalizer(new CacheNormalizer());
            $normalizer->addNormalizer(new TemplateNormalizer());
            $normalizer->addNormalizer(new PresenterNormalizer());
            $normalizer->addNormalizer(new ControllerNormalizer());
            $normalizer->addNormalizer(new ContextAsNormalizer());
            $normalizer->addNormalizer(new CacheTagsNormalizer());
            return $normalizer;
        });

        $this->declareAsSingleton($app);
    }
}
