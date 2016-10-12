<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 15/9/15
 * Time: 下午2:30
 */
namespace Canoe\Event;

use Canoe\Util\TypeUtils;

trait EventTrigger
{
    /**
     * 侦听器们
     * @var EventHandlerEntry[][]
     */
    private $eventEntriesMap = [];

    /**
     * 添加事件回调
     *
     * @param string $eventClass 事件类型
     * @param callable $handler 事件回调
     * @param int $priority 优先级
     *
     * @return callable 返回添加的事件回调
     */
    public function on($eventClass, callable $handler, $priority = 0)
    {
        $this->validateEventClass(__METHOD__, 1, $eventClass);
        TypeUtils::validateCallable($handler, $eventClass);

        $exists = false;
        /** @var EventHandlerEntry[] $entries */
        $entries = &$this->eventEntriesMap[$eventClass];
        if (!isset($entries)) {
            $entries = [];
        } else {
            foreach ($entries as $entry) {
                if ($entry->getHandler() == $handler) {
                    $entry->setPriority($priority);
                    $exists = true;
                }
            }
        }

        if (!$exists) {
            $entries[] = new EventHandlerEntry($handler, $priority);
        }

        return $handler;
    }

    /**
     * 删除事件回调
     * @param string $eventClass
     * @param callable $handler
     */
    public function off($eventClass, callable $handler)
    {
        $this->validateEventClass(__METHOD__, 1, $eventClass);
        /** @var EventHandlerEntry[] $entries */
        $entries = &$this->eventEntriesMap[$eventClass];
        if (!isset($entries)) {
            return;
        }

        foreach ($entries as $key => $entry) {
            if ($entry->getHandler() == $handler) {
                unset($entries[$key]);
                break;
            }
        }
    }

    /**
     * 触发事件
     *
     * @param Event $event 派发的事件
     *
     * @return bool 如果事件被取消返回false, 否则返回true, 可用于在事件处理阶段改变事件发生后的后续行为
     */
    public function trigger(Event $event)
    {
        $event->setTrigger($this);

        $eventClass = get_class($event);
        /** @var EventHandlerEntry[] $entries */
        $entries = $this->fetchEntries($eventClass);

        usort($entries, function (EventHandlerEntry $entry1, EventHandlerEntry $entry2) {
            return $entry2->getPriority() - $entry1->getPriority();
        });

        $canceled = false;
        $flowStopped = false;
        /** @var EventHandlerEntry $entry */
        $entry = null;
        foreach ($entries as $entry) {
            $entry($event);
            $canceled = $canceled || $event->isCanceled();
            if ($event->isFlowStopped()) {
                $flowStopped = true;
                break;
            }
        }

        if (!$flowStopped && !($this instanceof EventSupervisorImpl)) {
            $canceled = !EventSupervisorImpl::getInstance()->trigger($event) || $canceled;
        }

        return !$canceled;
    }

    private function validateEventClass($method, $index, $eventClass)
    {
        if (!is_string($eventClass) || !class_exists($eventClass)
            || ($eventClass != Event::class && !is_subclass_of($eventClass, Event::class))
        ) {
            throw new \InvalidArgumentException(
                "Argument $index passed to $method must be a subclass of " . Event::class . ", $eventClass given"
            );
        }
    }

    private function fetchEntries($eventClass)
    {
        $entries = [];
        foreach ($this->eventEntriesMap as $key => $subEntries) {
            if ($key == $eventClass || is_subclass_of($eventClass, $key)) {
                $entries = array_merge($entries, $subEntries);
            }
        }

        return $entries;
    }

    public function __toString()
    {
        return static::class;
    }
}
