<?php

namespace Webmamba\OneFileTwigComponentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class OneFileTwigComponentExtension extends Extension implements ConfigurationInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->findDefinition('one_file_twig_component.assets_compiler')
            ->replaceArgument(2, $config['compiled_assets_path'])
        ;

        $container->findDefinition('one_file_twig_component.request_listener')
            ->replaceArgument(0, $config['compiled_assets_path'])
        ;
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return $this;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('one_file_twig_component');
        $rootNode = $treeBuilder->getRootNode();
        \assert($rootNode instanceof ArrayNodeDefinition);

        $rootNode
            ->children()
                ->scalarNode('compiled_assets_path')
                    ->info('Defaults to `./var/component-assets/component.css`')
                    ->defaultValue('%kernel.project_dir%/var/component-assets')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}