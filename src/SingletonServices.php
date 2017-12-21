<?php

namespace Imanghafoori\Widgets;

use Imanghafoori\Widgets\Utils\Normalizer;
use Imanghafoori\Widgets\Utils\Normalizers\CacheNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\TemplateNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ContextAsNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\PresenterNormalizer;
use Imanghafoori\Widgets\Utils\Normalizers\ControllerNormalizer;

class SingletonServices
{
    /**
     * Register classes as singletons.
     */
    public function registerSingletons($app)
    {
        $app->singleton('command.imanghafoori.widget', function ($app) {
            return $app['Imanghafoori\Widgets\WidgetGenerator'];
        });

        $app->singleton(Normalizer::class, function () {
            $cacheNormalizer = new CacheNormalizer();
            $tplNormalizer = new TemplateNormalizer();
            $presenterNormalizer = new PresenterNormalizer();
            $ctrlNormalizer = new ControllerNormalizer();
            $contextAsNormalizer = new ContextAsNormalizer();

            return new Utils\Normalizer($tplNormalizer, $cacheNormalizer, $presenterNormalizer, $ctrlNormalizer, $contextAsNormalizer);
        });

        $app->singleton(Utils\HtmlMinifier::class, function () {
            return new Utils\HtmlMinifier();
        });

        $app->singleton(Utils\DebugInfo::class, function () {
            return new Utils\DebugInfo();
        });

        $app->singleton(Utils\Policies::class, function () {
            return new Utils\Policies();
        });

        $app->singleton(Utils\Cache::class, function () {
            return new Utils\Cache();
        });

        $app->singleton(Utils\CacheTag::class, function () {
            return new Utils\CacheTag();
        });

        $app->singleton(Utils\WidgetRenderer::class, function () {
            return new Utils\WidgetRenderer();
        });
    }
}
