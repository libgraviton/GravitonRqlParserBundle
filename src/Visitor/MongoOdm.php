<?php
/**
 * MongoOdm class file
 */

namespace Graviton\RqlParserBundle\Visitor;

use Doctrine\MongoDB\Query\Builder;
use Doctrine\MongoDB\Query\Expr;
use Graviton\Rql\Visitor\MongoOdm as BaseVisitor;
use Graviton\RqlParserBundle\Rql\Node\ElemMatchNode;

/**
 * We extend base visitor to add support of elemMatch() operator
 *
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class MongoOdm extends BaseVisitor
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->internalMap['Graviton\RqlParserBundle\Rql\Node\ElemMatchNode'] = 'visitElemMatch';
    }

    /**
     * Visit elemMatch() node
     *
     * @param ElemMatchNode $node elemMatch() node
     * @param bool          $expr should i wrap this in expr()
     * @return Builder|Expr
     */
    protected function visitElemMatch(ElemMatchNode $node, $expr = false)
    {
        return $this
            ->getField($node->getField(), $expr)
            ->elemMatch($this->recurse($node->getQuery(), true));
    }
}
