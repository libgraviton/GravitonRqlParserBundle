<?php
/**
 * load services into di
 */

namespace Graviton\RqlParserBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class GravitonRqlParserExtension extends Extension
{
    /**
     * load services into di
     *
     * @param array            $config    config
     * @param ContainerBuilder $container containerbuilder
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
             new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');
    }
}
