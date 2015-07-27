<?php
/**
 * validate factory
 */

namespace Graviton\RqlParserBundle\Tests;

use lapistano\ProxyObject\ProxyBuilder;
use Xiag\Rql\Parser\Exception\SyntaxErrorException;

/**
 * @author List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://swisscom.ch
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * validate create method
     *
     * @return void
     */
    public function testCreate()
    {
        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setProperties(array('supportedVisitors'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\Graviton\RqlParserBundle\Tests\Fixtures\NoopVisitor';

        $this->assertInstanceOf('Graviton\Rql\Visitor\VisitorInterface', $factory->create('NoOp'));
    }

    /**
     * @dataProvider visitorNameProvider
     *
     * @param string $name class name
     *
     * @return void
     */
    public function testInitVisitor($name)
    {
        $queryBuilderDouble = $this->getMockBuilder('\Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setProperties(array('supportedVisitors'))
            ->setMethods(array('initVisitor'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\Graviton\RqlParserBundle\Tests\Fixtures\NoopVisitor';

        $this->assertInstanceOf(
            '\Graviton\Rql\Visitor\VisitorInterface',
            $factory->initVisitor($name, $queryBuilderDouble)
        );
    }

    /**
     * provide data
     *
     * @return array
     */
    public function visitorNameProvider()
    {
        return array(
            'NoOp' => array('NoOp'),
            'MongoODM' => array('MongoODM'),
        );
    }

    /**
     * validate supportsClass method
     *
     * @return void
     */
    public function testSupportsClass()
    {
        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setMethods(array('supportsClass'))
            ->getProxy();

        $this->setExpectedException('\Graviton\RqlParserBundle\Exceptions\VisitorNotSupportedException');

        $factory->supportsClass('NoSupported');
    }

    /**
     * validate visitor interface implementation
     *
     * @return void
     */
    public function testClassImplementsVisitorInterface()
    {
        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setProperties(array('supportedVisitors'))
            ->setMethods(array('classImplementsVisitorInterface'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\stdClass';

        $this->setExpectedException('\Graviton\RqlParserBundle\Exceptions\VisitorInterfaceNotImplementedException');

        $factory->classImplementsVisitorInterface('NoOp');
    }

    /**
     * @param string $class name of class to proxy
     *
     * @return ProxyBuilder
     */
    public function getProxyBuilder($class)
    {
        return new ProxyBuilder($class);
    }
}
