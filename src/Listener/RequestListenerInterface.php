<?php
/**
 * RequestListenerInterface class file
 */

namespace Graviton\RqlParserBundle\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
interface RequestListenerInterface
{
    /**
     * Process RQL query
     *
     * @param RequestEvent $event Event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event);
}
