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
     * @param RestEvent $event Event
     *
     * @return void|null
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $filter = null;

        // grab unencoded version of rql extract q arg
        // has to grab the query direclty from _SERVER so it does not get unecoded by php beforehand
        $filter = $request->query->get('q');
        if (array_key_exists('QUERY_STRING', $_SERVER)) {
            $filter = array_filter(
                explode('&', $_SERVER['QUERY_STRING']),
                function ($param) {
                    return (substr($param, 0, 2) == $this->queryKey.'=');
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
