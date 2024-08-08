<?php
/**
 * GravitonRqlParserBundle integrate RQL services with symfony.
 */

namespace Graviton\RqlParserBundle;

use Graviton\RqlParserBundle\DependencyInjection\Compiler\RqlParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class GravitonRqlParserBundle extends Bundle
{
    /**
     * load version compiler pass
     *
     * @param ContainerBuilder $container container builder
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RqlParserCompilerPass());
    }
}
