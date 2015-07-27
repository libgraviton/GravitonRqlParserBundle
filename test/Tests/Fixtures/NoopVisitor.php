<?php
/**
 * null visitor
 */

namespace Graviton\RqlParserBundle\Tests\Fixtures;

use Graviton\Rql\Visitor\VisitorInterface;
use Xiag\Rql\Parser\Query;

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
     * @param Query $query query AST
     *
     * @return void
     */
    public function visit(Query $query)
    {
        return;
    }
}
