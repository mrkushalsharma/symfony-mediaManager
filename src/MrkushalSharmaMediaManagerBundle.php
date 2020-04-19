<?php


namespace MrkushalSharma\MediaManager;


use MrkushalSharma\MediaManager\DependencyInjection\MediaManagerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MrkushalSharmaMediaManagerBundle extends Bundle
{

    /**
     * {@inheritdoc}
     * @return MediaManagerExtension
     */
    public function getContainerExtension()
    {
        $class = $this->getContainerExtensionClass();

        return new $class;
    }


    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensionClass()
    {
        return MediaManagerExtension::class;
    }

}