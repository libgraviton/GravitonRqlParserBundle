<?php
/**
 * RequestListenerInterface class file
 */

namespace Graviton\RqlParserBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author   List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
interface RequestListenerInterface
{
    /**
     * Process RQL query
     *
     * @param GetResponseEvent $event Event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event);
}
