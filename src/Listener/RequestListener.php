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
class RequestListener
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
     * @var string
     */
    private $queryKey;

    /**
     * @param Lexer  $lexer    rql lexer
     * @param Parser $parser   rql parser
     * @param string $queryKey name of query attribute to use
     */
    public function __construct(Lexer $lexer, Parser $parser, $queryKey)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
        $this->queryKey = $queryKey;
    }

    /**
     * Validate the json input to prevent errors in the following components
     *
     * @param GetResponseEvent $event Event
     *
     * @return void|null
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // grab unencoded version of rql extract q arg
        // has to grab the query direclty from _SERVER so it does not get unecoded by php beforehand
        $filter = null;
        if ($request->server->get('QUERY_STRING') !== null) {
            $filter = array_filter(
                explode('&', $request->server->get('QUERY_STRING')),
                function ($param) {
                    return (substr($param, 0, 2) == $this->queryKey . '=');
                }
            );
            $filter = substr(reset($filter), 2);
        }
        if (empty($filter)) {
            return;
        }
        $request->attributes->set('hasRql', true);
        $request->attributes->set('rawRql', $filter);
        $request->attributes->set('rqlQuery', $this->parser->parse($this->lexer->tokenize($filter)));
    }
}
