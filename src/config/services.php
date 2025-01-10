<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webmamba\OneFileTwigComponentBundle\AssetsComponentRegistry;
use Webmamba\OneFileTwigComponentBundle\TwigComponentAssetExtractor;
use Webmamba\OneFileTwigComponentBundle\TwigComponentAssetsCompiler;
use Webmamba\OneFileTwigComponentBundle\EventListener\RequestListener;
use Webmamba\OneFileTwigComponentBundle\EventListener\TwigComponentAssetsListener;
use Webmamba\OneFileTwigComponentBundle\EventListener\TwigComponentImportMapListener;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('cache.one_file_twig_component')
            ->parent('cache.system')
            ->tag('cache.pool')
        ->set('one_file_twig_component.assets_component_registry', AssetsComponentRegistry::class)
        ->set('one_file_twig_component.assets_extractor', TwigComponentAssetExtractor::class)
        ->set('one_file_twig_component.assets_compiler', TwigComponentAssetsCompiler::class)
            ->args([
                service('cache.one_file_twig_component'),
                service('one_file_twig_component.assets_extractor'),
                abstract_arg('Assets directory path')
            ])
        ->set('one_file_twig_component.request_listener', RequestListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                abstract_arg('Compiled Assets path')
            ])
        ->set('one_file_twig_component.assets_listener', TwigComponentAssetsListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                service('twig'),
                service('one_file_twig_component.assets_compiler'),
                service('one_file_twig_component.assets_component_registry')
            ])
        ->set('one_file_twig_component.import_map_listener', TwigComponentImportMapListener::class)
        ->tag('kernel.event_subscriber')
        ->args([
            service('one_file_twig_component.assets_component_registry')
        ])
    ;
};
