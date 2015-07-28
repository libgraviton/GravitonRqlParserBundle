<?php
/**
 * validate listener
 */

namespace Graviton\RqlParserBundle\Tests\Listener;

use Graviton\RqlParserBundle\Listener\RequestListener;

/**
 * @author List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://swisscom.ch
 */
class RequestListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * check if nothing gets done when query is empty
     */
    public function testWillBehaveOnEmptyQuery()
    {
        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $parserDouble = $this->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $eventDouble = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $requestDouble = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $queryDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $queryDouble->expects($this->once())
            ->method('get')
            ->with('q')
            ->willReturn('');
        $requestDouble->query = $queryDouble;

        $attributesDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $attributesDouble->expects($this->never())
            ->method('set');
        $requestDouble->attributes = $attributesDouble;

        $eventDouble->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestDouble);

        $sut = new RequestListener(
            $lexerDouble,
            $parserDouble,
            'q'
        );

        $sut->onKernelRequest($eventDouble);
    }

    /**
     * test if queries get handled properly
     */
    public function testWillParseQuery()
    {
        $lexerDouble = $this->getMock('Xiag\Rql\Parser\Lexer');
        $lexerDouble->expects($this->any())
            ->method('tokenize')
            ->willReturn(
                $this->getMockBuilder('Xiag\Rql\Parser\TokenStream')
                    ->disableOriginalConstructor()
                    ->getMock()
            );

        $parserDouble = $this->getMockBuilder('Xiag\Rql\Parser\Parser')
            ->disableOriginalConstructor()
            ->getMock();
        $parserDouble->expects($this->once())
            ->method('parse')
            ->willReturn($this->getMock('Xiag\Rql\Parser\Query'));

        $eventDouble = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $requestDouble = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $queryDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $queryDouble->expects($this->once())
            ->method('get')
            ->with('q')
            ->willReturn('eq(foo,bar)');
        $requestDouble->query = $queryDouble;

        $attributesDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $attributesDouble->expects($this->at(0))
            ->method('set')
            ->with('hasRql', true);
        $attributesDouble->expects($this->at(1))
            ->method('set')
            ->with('rawRql', 'eq(foo,bar)');
        $attributesDouble->expects($this->at(2))
            ->method('set')
            ->with('rqlQuery', $this->isInstanceOf('Xiag\Rql\Parser\Query'));
        $requestDouble->attributes = $attributesDouble;

        $eventDouble->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestDouble);

        $sut = new RequestListener(
            $lexerDouble,
            $parserDouble,
            'q'
        );

        $sut->onKernelRequest($eventDouble);
    }
}
