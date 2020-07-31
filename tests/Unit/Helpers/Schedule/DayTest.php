<?php

namespace Handtuchsystem\Test\Unit\Helpers\Schedule;

use Carbon\Carbon;
use Handtuchsystem\Helpers\Schedule\Day;
use Handtuchsystem\Helpers\Schedule\Room;
use Handtuchsystem\Test\Unit\TestCase;

class DayTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Day::__construct
     * @covers \Handtuchsystem\Helpers\Schedule\Day::getDate
     * @covers \Handtuchsystem\Helpers\Schedule\Day::getStart
     * @covers \Handtuchsystem\Helpers\Schedule\Day::getEnd
     * @covers \Handtuchsystem\Helpers\Schedule\Day::getIndex
     * @covers \Handtuchsystem\Helpers\Schedule\Day::getRoom
     */
    public function testCreate()
    {
        $day = new Day(
            '2000-01-01',
            new Carbon('2000-01-01T03:00:00+01:00'),
            new Carbon('2000-01-02T05:59:00+00:00'),
            1
        );
        $this->assertEquals('2000-01-01', $day->getDate());
        $this->assertEquals('2000-01-01T03:00:00+01:00', $day->getStart()->format(Carbon::RFC3339));
        $this->assertEquals('2000-01-02T05:59:00+00:00', $day->getEnd()->format(Carbon::RFC3339));
        $this->assertEquals(1, $day->getIndex());
        $this->assertEquals([], $day->getRoom());

        $rooms = [
            new Room('Foo'),
            new Room('Bar'),
        ];
        $day = new Day(
            '2001-01-01',
            new Carbon('2001-01-01T03:00:00+01:00'),
            new Carbon('2001-01-02T05:59:00+00:00'),
            1,
            $rooms
        );
        $this->assertEquals($rooms, $day->getRoom());
    }
}
