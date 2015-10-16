<?php
/**
 * ElemMatchNode class file
 */

namespace Graviton\RqlParserBundle\Rql\Node;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * elemMatch() node
 *
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class ElemMatchNode extends AbstractQueryNode
{
    /**
     * @var string
     */
    private $field;
    /**
     * @var AbstractQueryNode
     */
    private $query;

    /**
     * Constructor
     *
     * @param string            $field Field
     * @param AbstractQueryNode $query Query
     */
    public function __construct($field, AbstractQueryNode $query)
    {
        $this->field = $field;
        $this->query = $query;
    }

    /**
     * Get node name
     *
     * @return string
     */
    public function getNodeName()
    {
        return 'elemMatch';
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get query
     *
     * @return AbstractQueryNode
     */
    public function getQuery()
    {
        return $this->query;
    }
}
