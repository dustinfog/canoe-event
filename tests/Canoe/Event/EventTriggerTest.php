<?php
/**
 * Created by PhpStorm.
 * User: panzd
 * Date: 12/10/2016
 * Time: 2:30 PM
 */
namespace Canoe\Event;


class EventTriggerTest extends \PHPUnit_Framework_TestCase
{
    use EventTrigger;

    public function say()
    {
        if ($this->trigger(new SayEvent())) {
            return "hello,world";
        }

        return "wow";
    }

    public function howl()
    {
        return $this->trigger(new HowlEvent());
    }

    public function testSimpleEvent()
    {
        $this->on(SayEvent::class, function (SayEvent $event) {
            echo "hello";
        });

        $this->expectOutputString("hello");
        $this->assertEquals("hello,world", $this->say());
    }

    public function testCancelEvent()
    {
        $this->on(SayEvent::class, function (SayEvent $event) {
            $event->cancel();
        });

        $this->assertEquals("wow", $this->say());
    }

    public function testGlobalListen()
    {
        EventSupervisor::on(SayEvent::class, function (Event $event) {
            echo get_class($event->getTrigger());
        });

        $this->expectOutputString(__CLASS__);
        $this->say();
    }

    public function testPriority()
    {
        $this->on(SayEvent::class, function(SayEvent $event) {
            echo '1';
        }, 1);
        $this->on(SayEvent::class, function(SayEvent $event) {
            echo '3';
        }, 3);
        $this->on(SayEvent::class, function(SayEvent $event) {
            echo '2';
        }, 2);

        $this->expectOutputString("123");
        $this->say();
    }
}

class SayEvent extends Event
{
}

class HowlEvent extends Event
{
}

