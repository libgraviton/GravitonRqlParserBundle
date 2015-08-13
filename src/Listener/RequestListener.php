<?php
/**
 * Listen for requests containing rql queries and store the resulting queries AST in the request
 */

namespace Graviton\RqlParserBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;

/**
 * @author   List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class RequestListener implements RequestListenerInterface
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param Lexer  $lexer  rql lexer
     * @param Parser $parser rql parser
     */
    public function __construct(Lexer $lexer, Parser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
    }

    /**
     * Process RQL query
     *
     * @param GetResponseEvent $event Event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // grab unencoded version of rql extract q arg
        // has to grab the query direclty from _SERVER so it does not get unecoded by php beforehand
        $filter = $request->server->get('QUERY_STRING');
        if (empty($filter)) {
            return;
        }
        $request->attributes->set('hasRql', true);
        $request->attributes->set('rawRql', $filter);
        $request->attributes->set('rqlQuery', $this->parser->parse($this->lexer->tokenize($filter)));
    }
}
