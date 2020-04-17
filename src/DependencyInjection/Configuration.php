<?php


namespace MrkushalSharma\MediaManager\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        // TODO: Implement getConfigTreeBuilder() method.
        $treeBuilder = new TreeBuilder('media_manager');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('media_manager');
        }

        $rootNode
            ->children()
            ->scalarNode('doctrine_manager')->defaultValue('default')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}