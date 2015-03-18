<?php

namespace Graviton\RqlParserBundle\Tests;

use lapistano\ProxyObject\ProxyBuilder;


class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $operationDouble = $this->getMockBuilder('\Graviton\Rql\AST\OperationInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('accept'))
            ->getMockForAbstractClass();
        $operationDouble
            ->expects($this->once())
            ->method('accept')
            ->with($this->isInstanceOf('\Graviton\Rql\Visitor\VisitorInterface'));

        $parserDouble = $this->getMockBuilder('\Graviton\Rql\Parse')
            ->disableOriginalConstructor()
            ->setMethods(array('getAST'))
            ->getMock();
        $parserDouble
            ->expects($this->once())
            ->method('getAST')
            ->willReturn($operationDouble);

        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setProperties(array('supportedVisitors', 'parser'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\Graviton\RqlParserBundle\Tests\Fixtures\NoopVisitor';
        $factory->parser = $parserDouble;

        $this->assertInstanceOf('\Graviton\Rql\Visitor\VisitorInterface',$factory->create('NoOp', ''));
    }

    /**
     * @dataProvider visitorNameProvider
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

    public function visitorNameProvider()
    {
        return array(
            'NoOp' => array('NoOp'),
            'MongoODM' => array('MongoODM'),
        );
    }

    public function testInitParser()
    {
        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setMethods(array('initParser'))
            ->getProxy();

        $this->assertInstanceOf('\Graviton\Rql\Parser', $factory->initParser(''));
    }

    public function testSupportsClass()
    {
        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setMethods(array('supportsClass'))
            ->getProxy();

        $this->setExpectedException('\Graviton\Rql\Exceptions\VisitorNotSupportedException');

        $factory->supportsClass('NoSupported');
    }

    public function testClassImplementsVisitorInterface()
    {
        $factory = $this->getProxyBuilder('\Graviton\RqlParserBundle\Factory')
            ->setProperties(array('supportedVisitors'))
            ->setMethods(array('classImplementsVisitorInterface'))
            ->getProxy();

        $factory->supportedVisitors['noop'] = '\stdClass';

        $this->setExpectedException('\Graviton\Rql\Exceptions\VisitorInterfaceNotImplementedException');

        $factory->classImplementsVisitorInterface('NoOp');
    }


    /**
     * @param string $class
     *
     * @return ProxyBuilder
     */
    public function getProxyBuilder($class)
    {
        return new ProxyBuilder($class);
    }
}
