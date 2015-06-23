<?php
/**
 * Generates Query objects to search a repository.
 */
namespace Graviton\RqlParserBundle;

use Doctrine\ODM\MongoDB\Query\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser as RqlParser;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;
use Graviton\RqlParserBundle\Exceptions\VisitorInterfaceNotImplementedException;
use Graviton\RqlParserBundle\Exceptions\VisitorNotSupportedException;
use Graviton\Rql\Parser;
use Graviton\Rql\Visitor\VisitorInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class Factory
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var RqlParser
     */
    private $rqlParser;

    /**
     * @var array Set of supported Visitors
     */
    protected $supportedVisitors = array(
        'mongoodm' => '\Graviton\Rql\Visitor\MongoOdm',
    );

    /**
     * @param Lexer     $lexer     lexer
     * @param RqlParser $rqlParser parser
     */
    public function __construct(Lexer $lexer, RqlParser $rqlParser)
    {
        $this->lexer = $lexer;
        $this->rqlParser = $rqlParser;
    }


    /**
     * Provides an instance of the RQL Visitor.
     *
     * @param string  $visitorName  Name of the visitor class
     * @param string  $rqlQuery     RQL formats string
     * @param Builder $queryBuilder Doctrine QueryBuilder
     *
     * @return Parser
     */
    public function create($visitorName, $rqlQuery, Builder $queryBuilder = null)
    {
        $visitor = $this->initVisitor($visitorName, $queryBuilder);
        $this->parser = $this->initParser($visitor);

        try {
            $this->parser->parse($rqlQuery);
        } catch (SyntaxErrorException $e) {
            throw new BadRequestHttpException('syntax error in rql query', $e);
        }

        return $this->parser;
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
     * @param VisitorInterface $visitor rql visitor
     *
     * @return Parser
     */
    protected function initParser(VisitorInterface $visitor)
    {
        return new Parser(
            $this->lexer,
            $this->rqlParser,
            $visitor
        );
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
