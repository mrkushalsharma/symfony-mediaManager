<?php


namespace MrkushalSharma\MediaManager\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MediaManagerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO: Implement load() method.
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
//        foreach ($config as $key => $value) {
//            $container->setParameter('media_manager.'.$key, $value);
//        }
    }


    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'media_manager';
    }
}