<?php
/** processes config */

namespace Graviton\RqlParserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
class RqlParserCompilerPass implements CompilerPassInterface
{

    /**
     *
     * @param ContainerBuilder $container Container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $hasListener = $container->getParameter('graviton_rqlparser.activate_listener');
        if (!$hasListener) {
            $container->removeDefinition('graviton.rql.listener.request');
        }
    }
}
