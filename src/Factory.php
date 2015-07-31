<?php
/**
 * Generates Query objects to search a repository.
 */
namespace Graviton\RqlParserBundle;

use Doctrine\ODM\MongoDB\Query\Builder;
use Graviton\RqlParserBundle\Exceptions\VisitorInterfaceNotImplementedException;
use Graviton\RqlParserBundle\Exceptions\VisitorNotSupportedException;
use Graviton\Rql\Visitor\VisitorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class Factory
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * @var array Set of supported Visitors
     */
    protected $supportedVisitors = array(
        'mongoodm' => '\Graviton\Rql\Visitor\MongoOdm',
    );

    /**
     * inject an optional event dispatcher
     *
     * If injected this is used to dispatch some lifecycle events that you may use
     * to hook into query visitation
     *
     * @param EventDispatcherInterface $dispatcher event dispatcher to dispatch events on
     *
     * @return void
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Provides an instance of the RQL Visitor.
     *
     * @param string  $visitorName  Name of the visitor class
     * @param Builder $queryBuilder Doctrine QueryBuilder
     *
     * @return Query
     */
    public function create($visitorName, Builder $queryBuilder = null)
    {
        return $this->initVisitor($visitorName, $queryBuilder);
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

        if ($this->dispatcher && is_callable(array($visitor, 'setDispatcher'))) {
            $visitor->setDispatcher($this->dispatcher);
        }

        return $visitor;
    }

    /**
     * Determines if the provided class is a supported visitor
     *
     * @param string $name class name to check
     *
     * @throws VisitorNotSupportedException
     *
     * @return void
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
     * @param string $name class name to check
     *
     * @throws VisitorInterfaceNotImplementedException
     *
     * @return void
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
