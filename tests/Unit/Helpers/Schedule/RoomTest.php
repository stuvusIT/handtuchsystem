<?php

namespace Handtuchsystem\Test\Unit\Helpers\Schedule;

use Handtuchsystem\Helpers\Schedule\Event;
use Handtuchsystem\Helpers\Schedule\Room;
use Handtuchsystem\Test\Unit\TestCase;

class RoomTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Room::__construct
     * @covers \Handtuchsystem\Helpers\Schedule\Room::getName
     * @covers \Handtuchsystem\Helpers\Schedule\Room::getEvent
     * @covers \Handtuchsystem\Helpers\Schedule\Room::setEvent
     */
    public function testCreate()
    {
        $room = new Room('Test');
        $this->assertEquals('Test', $room->getName());
        $this->assertEquals([], $room->getEvent());

        $events = [$this->createMock(Event::class), $this->createMock(Event::class)];
        $events2 = [$this->createMock(Event::class)];
        $room = new Room('Test2', $events);
        $this->assertEquals($events, $room->getEvent());

        $room->setEvent($events2);
        $this->assertEquals($events2, $room->getEvent());
    }
}
