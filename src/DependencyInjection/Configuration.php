<?php

namespace Graviton\RqlParserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder('graviton_rqlparser');
        $treeBuilder->getRootNode()
                        ->children()
                            ->booleanNode('activate_listener')->defaultTrue()->end()
                        ->end();

        return $treeBuilder;
    }
}
