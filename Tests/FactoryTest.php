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
        $parserDouble = $this->getMockBuilder('\Graviton\Rql\Parser')
            ->disableOriginalConstructor()
            ->setMethods(array('parse', 'buildQuery'))
            ->getMock();

        $tokenDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenStream')
            ->disableOriginalConstructor()
            ->getMock();

        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');

        $lexerDouble->expects($this->once())
            ->method('tokenize')
            ->willReturn($tokenDouble);

        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
            ->setProperties(array('supportedVisitors', 'parser'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\Graviton\RqlParserBundle\Tests\Fixtures\NoopVisitor';
        $factory->parser = $parserDouble;

        $this->assertInstanceOf('Graviton\Rql\Parser', $factory->create('NoOp', ''));
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

        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
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
     * validate parser initialization
     *
     * @return void
     */
    public function testInitParser()
    {
        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
            ->setMethods(array('initParser'))
            ->getProxy();

        $visitorDouble = $this->getMock('Graviton\Rql\Visitor\VisitorInterface');

        $this->assertInstanceOf('\Graviton\Rql\Parser', $factory->initParser($visitorDouble));
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
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
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
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
            ->setProperties(array('supportedVisitors'))
            ->setMethods(array('classImplementsVisitorInterface'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\stdClass';

        $this->setExpectedException('\Graviton\RqlParserBundle\Exceptions\VisitorInterfaceNotImplementedException');

        $factory->classImplementsVisitorInterface('NoOp');
    }

    /**
     * validate that syntax errors are rethrown as bar request exceptions
     *
     * @return void
     */
    public function testCreateThrowsExceptionOnInvalidRql()
    {
        $parserDouble = $this->getMockBuilder('\Graviton\Rql\Parser')
            ->disableOriginalConstructor()
            ->setMethods(array('parse', 'buildQuery'))
            ->getMock();

        $tokenDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\TokenStream')
            ->disableOriginalConstructor()
            ->getMock();

        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');

        $lexerDouble->expects($this->once())
            ->method('tokenize')
            ->willReturn($tokenDouble);

        $rqlParserDouble = $this
            ->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $rqlParserDouble
            ->expects($this->once())
            ->method('parse')
            ->will($this->throwException(new SyntaxErrorException));

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setConstructorArgs(
                [
                    $lexerDouble,
                    $rqlParserDouble
                ]
            )
            ->setProperties(array('supportedVisitors', 'parser'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\Graviton\RqlParserBundle\Tests\Fixtures\NoopVisitor';
        $factory->parser = $parserDouble;

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\BadRequestHttpException');

        $factory->create('NoOp', 'invalide=rql&inout(=];');
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
