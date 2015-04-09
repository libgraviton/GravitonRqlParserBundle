<?php
/**
 * null visitor
 */

namespace Graviton\RqlParserBundle\Tests\Fixtures;

use Graviton\Rql\AST\OperationInterface;
use Graviton\Rql\Visitor\VisitorInterface;

/**
 * @author List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://swisscom.ch
 */
class NoopVisitor implements VisitorInterface
{
    /**
     * null visitor
     *
     * @param OperationInterface $operation operation AST
     *
     * @return void
     */
    public function visit(OperationInterface $operation)
    {
        return;
    }
}
