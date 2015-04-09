<?php
/**
 * Generates Query objects to search a repository.
 */
namespace Graviton\RqlParserBundle;

use Doctrine\ODM\MongoDB\Query\Builder;
use Graviton\Rql\Exceptions\VisitorInterfaceNotImplementedException;
use Graviton\Rql\Exceptions\VisitorNotSupportedException;
use Graviton\Rql\Parser;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class Factory
{
    /** @var Parser */
    protected $parser;

    /** @var array Set of supported Visitors */
    protected $supportedVisitors = array(
        'mongoodm' => '\Graviton\Rql\Visitor\MongoOdm',
    );

    /**
     * @param string  $visitorName  Name of the visitor class
     * @param string  $rqlQuery     RQL formats string
     * @param Builder $queryBuilder Doctrine QueryBuilder
     *
     * @return \Graviton\Rql\Visitor\VisitorInterface
     */
    public function create($visitorName, $rqlQuery, Builder $queryBuilder = null)
    {
        $visitor = $this->initVisitor($visitorName, $queryBuilder);
        $parser = $this->initParser($rqlQuery);

        $ast = $parser->getAST();
        $ast->accept($visitor);

        return $visitor;
    }

    /**
     * Provides an instance of the requested visitor.
     *
     * @param string  $name         Classname of the visitor to be initialized
     * @param Builder $queryBuilder Doctrine QueryBuilder
     *
     * @return \Graviton\Rql\Visitor\VisitorInterface
     */
    protected function initVisitor($name, Builder $queryBuilder = null)
    {
        $this->supportsClass($name);
        $this->classImplementsVisitorInterface($name);

        $lcName = strtolower($name);
        $visitorClass = $this->supportedVisitors[$lcName];

        switch ($lcName) {
            case 'mongoodm':
                $visitor = new $visitorClass($queryBuilder);
                break;
            default:
                $visitor = new $visitorClass();
        }

        return $visitor;
    }

    /**
     * Provides an instance of the Rql Parser.
     *
     * @param string $query RQL formatted string.
     *
     * @return Parser
     */
    protected function initParser($query)
    {
        if (empty($this->parser)) {
            $this->parser = new Parser($query);
        }
        return $this->parser;
    }

    /**
     * Determines if the provided
     *
     * @param string $name
     *
     * @throws VisitorNotSupportedException
     */
    protected function supportsClass($name)
    {
        if (!array_key_exists(strtolower($name), $this->supportedVisitors)) {
            throw new VisitorNotSupportedException(
                sprintf('Provided name (%s) is not a supported visitor.', $name)
            );
        }
    }

    /**
     * Determines that the provided class is a valid visitor.
     *
     * @param string $name
     *
     * @throws VisitorInterfaceNotImplementedException
     */
    protected function classImplementsVisitorInterface($name)
    {
        $reflection = new \ReflectionClass($this->supportedVisitors[strtolower($name)]);

        if (!$reflection->implementsInterface('\Graviton\Rql\Visitor\VisitorInterface')) {
            throw new VisitorInterfaceNotImplementedException(
                sprintf('Provided visitor (%s) does not implement the VisitorInterface.', $name)
            );
        }
    }
}
