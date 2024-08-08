<?php
/**
 * Listen for requests containing rql queries and store the resulting queries AST in the request
 */

namespace Graviton\RqlParserBundle\Listener;

use Graviton\RqlParserBundle\Component\RequestParser;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class RequestListener
{
    /**
     * @var RequestParser
     */
    private RequestParser $requestParser;

    /**
     * @param RequestParser $requestParser
     */
    public function __construct(RequestParser $requestParser)
    {
        $this->requestParser = $requestParser;
    }

    /**
     * Process RQL query
     *
     * @param RequestEvent $event Event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $result = $this->requestParser->parse($event->getRequest());
        $request = $event->getRequest();
        if ($result->isHasRql()) {
            $request->attributes->set('hasRql', true);
            $request->attributes->set('rawRql', $result->getRawRql());
            $request->attributes->set('rqlQuery', $result->getRqlQuery());
        }
    }
}
