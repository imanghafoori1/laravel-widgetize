<?php

namespace Imanghafoori\Widgets;

use Illuminate\Container\Container;
use Imanghafoori\Widgets\Utils\Normalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheTagsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
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
    private $singletonClasses  = [
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
            $cacheNormalizer = new CacheNormalizer();
            $tplNormalizer = new TemplateNormalizer();
            $presenterNormalizer = new PresenterNormalizer();
            $ctrlNormalizer = new ControllerNormalizer();
            $contextAsNormalizer = new ContextAsNormalizer();
            $cacheTagsNormalizer = new CacheTagsNormalizer();

            return new Utils\Normalizer(
                $tplNormalizer,
                $cacheNormalizer,
                $presenterNormalizer,
                $ctrlNormalizer,
                $cacheTagsNormalizer,
                $contextAsNormalizer
            );
        });

        $this->declareAsSingleton($app);
    }
}
