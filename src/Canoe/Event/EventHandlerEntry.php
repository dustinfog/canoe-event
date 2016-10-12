<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 8/24/16
 * Time: 3:40 PM
 */

namespace Canoe\Event;

/**
 * Class EventHandlerEntry
 * @package Canoe\Event
 * @internal
 */
class EventHandlerEntry
{
    private $priority = 0;
    private $handler;

    /**
     * EventHandlerEntry constructor.
     * @param callable $handler
     * @param int $priority
     *
     * @internal
     */
    public function __construct(callable $handler, $priority = 0)
    {
        $this->handler = $handler;
        $this->priority = intval($priority);
    }

    /**
     * @param int $priority
     * @interna
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     * @interna
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return callable
     * @interna
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param Event $event
     * @internal
     */
    public function __invoke(Event $event)
    {
        call_user_func($this->handler, $event);
    }
}