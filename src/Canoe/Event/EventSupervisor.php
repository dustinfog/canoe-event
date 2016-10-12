<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 8/23/16
 * Time: 7:55 PM
 */

namespace Canoe\Event;

final class EventSupervisor
{
    /**
     * 添加事件回调
     *
     * @param string $eventClass 事件类型
     * @param callable $handler 事件回调
     * @param int $priority 优先级
     *
     * @return callable 返回添加的事件回调
     */
    public static function on($eventClass, callable $handler, $priority = 0)
    {
        return EventSupervisorImpl::getInstance()->on($eventClass, $handler, $priority);
    }

    /**
     * 删除事件回调
     * @param string $eventClass
     * @param callable $handler
     */
    public static function off($eventClass, callable $handler)
    {
        EventSupervisorImpl::getInstance()->off($eventClass, $handler);
    }
}
