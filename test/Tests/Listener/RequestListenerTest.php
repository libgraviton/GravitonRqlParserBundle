<?php
/**
 * validate listener
 */

namespace Graviton\RqlParserBundle\Tests\Listener;

use Graviton\RqlParserBundle\Listener\RequestListener;
use PHPUnit\Framework\TestCase;

/**
 * @author List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://swisscom.ch
 */
class RequestListenerTest extends TestCase
{
    /**
     * check if nothing gets done when query is empty
     *
     * @return void
     */
    public function testWillBehaveOnEmptyQuery()
    {
        $lexerDouble = $this->getMockBuilder('Graviton\RqlParser\Lexer')->getMock();
        $parserDouble = $this->getMockBuilder('Graviton\RqlParser\Parser')
            ->disableOriginalConstructor()
            ->getMock();

        $eventDouble = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $requestDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $serverDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\ServerBag')->getMock();
        $serverDouble->expects($this->once())
            ->method('get')
            ->with('QUERY_STRING')
            ->willReturn(null);
        $requestDouble->server = $serverDouble;

        $attributesDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')->getMock();
        $attributesDouble->expects($this->never())
            ->method('set');
        $requestDouble->attributes = $attributesDouble;

        $eventDouble->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestDouble);

        $sut = new RequestListener(
            $lexerDouble,
            $parserDouble
        );

        $sut->onKernelRequest($eventDouble);
    }

    /**
     * test if queries get handled properly
     *
     * @dataProvider willParseQueryData
     *
     * @param string $query that we should be extracting
     *
     * @return void
     */
    public function testWillParseQuery($query)
    {
        $lexerDouble = $this->getMockBuilder('Graviton\RqlParser\Lexer')->getMock();
        $lexerDouble->expects($this->any())
            ->method('tokenize')
            ->willReturn(
                $this->getMockBuilder('Graviton\RqlParser\TokenStream')
                    ->disableOriginalConstructor()
                    ->getMock()
            );

        $parserDouble = $this->getMockBuilder('Graviton\RqlParser\Parser')
            ->disableOriginalConstructor()
            ->getMock();
        $parserDouble->expects($this->once())
            ->method('parse')
            ->willReturn($this->getMockBuilder('Graviton\RqlParser\Query')->getMock());

        $eventDouble = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $requestDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();

        $serverDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\ServerBag')->getMock();
        $serverDouble->expects($this->once())
            ->method('get')
            ->with('QUERY_STRING')
            ->willReturn($query);
        $requestDouble->server = $serverDouble;

        $attributesDouble = $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')->getMock();
        $attributesDouble->expects($this->at(0))
            ->method('set')
            ->with('hasRql', true);
        $attributesDouble->expects($this->at(1))
            ->method('set')
            ->with('rawRql', $query);
        $attributesDouble->expects($this->at(2))
            ->method('set')
            ->with('rqlQuery', $this->isInstanceOf('Graviton\RqlParser\Query'));
        $requestDouble->attributes = $attributesDouble;

        $eventDouble->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestDouble);

        $sut = new RequestListener(
            $lexerDouble,
            $parserDouble
        );

        $sut->onKernelRequest($eventDouble);
    }

    /**
     * @return array[]
     */
    public function willParseQueryData()
    {
        return [
            'simple query string' => [
                'eq(foo,b%20a%20r)'
            ],
            'test with $ref in name' => [
                'eq(name.%24ref,http%3A%2F%2Fexmaple.com)'
            ],
            'multiple rql statements' => [
                '(a=2&(b<3|c>4)&like(e,123))&select(a,b)&sort(+a,-b)&limit(1,2)'
            ],
        ];
    }
}
