<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 15/9/15
 * Time: 下午3:50
 */
namespace Canoe\Event;

abstract class Event
{
    private $trigger;
    private $canceled = false;
    private $flowStopped = false;

    /**
     * @return EventTrigger
     */
    public function getTrigger()
    {
        return $this->trigger;
    }
    /**
     * @param EventTrigger $trigger
     */
    public function setTrigger($trigger)
    {
        if (empty($this->trigger)) {
            $this->trigger = $trigger;
        }
    }

    /**
     * @return boolean
     */
    public function isCanceled()
    {
        return $this->canceled;
    }

    /**
     * 执行该函数会直接导致 EventTrigger::trigger() 函数返回为false
     * @see EventTrigger::trigger()
     */
    public function cancel()
    {
        $this->canceled = true;
    }

    /**
     * @return boolean
     */
    public function isFlowStopped()
    {
        return $this->flowStopped;
    }

    /**
     * 停止事件流,执行该方法会阻止与当前事件关联的后续句柄的执行
     */
    public function stopFlow()
    {
        $this->flowStopped = true;
    }

    public function __toString()
    {
        return static::class;
    }
}