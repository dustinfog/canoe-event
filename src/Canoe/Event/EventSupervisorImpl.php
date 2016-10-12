<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 8/25/16
 * Time: 4:23 PM
 */

namespace Canoe\Event;

/**
 * Class EventSupervisorImpl
 * @package Canoe\Event
 * @internal
 */
final class EventSupervisorImpl
{
    use EventTrigger;
    /**
     * Instances of self
     * @var EventSupervisorImpl
     */
    private static $instance;
    /**
     * Get the instance
     * @return EventSupervisorImpl
     * @internal
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }
}