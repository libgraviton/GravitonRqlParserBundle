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
     *
     * @return void
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

        $serverDouble = $this->getMock('Symfony\Component\HttpFoundation\ServerBag');
        $serverDouble->expects($this->once())
            ->method('get')
            ->with('QUERY_STRING')
            ->willReturn(null);
        $requestDouble->server = $serverDouble;

        $attributesDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
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
     * @param string $raw   raw query string as would be in _SERVER
     * @param string $query what we should be extracting
     *
     * @return void
     */
    public function testWillParseQuery($raw, $query)
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

        $serverDouble = $this->getMock('Symfony\Component\HttpFoundation\ServerBag');
        $serverDouble->expects($this->once())
            ->method('get')
            ->with('QUERY_STRING')
            ->willReturn($raw);
        $requestDouble->server = $serverDouble;

        $attributesDouble = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $attributesDouble->expects($this->at(0))
            ->method('set')
            ->with('hasRql', true);
        $attributesDouble->expects($this->at(1))
            ->method('set')
            ->with('rawRql', $query);
        $attributesDouble->expects($this->at(2))
            ->method('set')
            ->with('rqlQuery', $this->isInstanceOf('Xiag\Rql\Parser\Query'));
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
            'simple query string' => ['eq(foo,b%20a%20r)', 'eq(foo,b%20a%20r)'],
            'string with paging stuff' => ['eq(foo,b%20a%20r)', 'eq(foo,b%20a%20r)'],
            'test with $ref in name' => [
                'eq(name.%24ref,http%3A%2F%2Fexmaple.com)',
                'eq(name.%24ref,http%3A%2F%2Fexmaple.com)'
            ],
            'multiple rql statements' => [
                '(a=2&(b<3|c>4)&like(e,123))&select(a,b)&sort(+a,-b)&limit(1,2)',
                '(a=2&(b<3|c>4)&like(e,123))&select(a,b)&sort(+a,-b)&limit(1,2)',
            ],
        ];
    }
}
