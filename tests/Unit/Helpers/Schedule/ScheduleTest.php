<?php

namespace Handtuchsystem\Test\Unit\Helpers\Schedule;

use Carbon\Carbon;
use Handtuchsystem\Helpers\Schedule\Conference;
use Handtuchsystem\Helpers\Schedule\Day;
use Handtuchsystem\Helpers\Schedule\Room;
use Handtuchsystem\Helpers\Schedule\Schedule;
use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;

class ScheduleTest extends TestCase
{
    use HasDatabase;

    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::__construct
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getVersion
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getConference
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getDay
     */
    public function testCreate()
    {
        $conference = new Conference('Foo Bar', 'FooB');
        $days = [$this->createMock(Day::class)];
        $schedule = new Schedule('Foo\'ing stuff 1.0', $conference, $days);

        $this->assertEquals('Foo\'ing stuff 1.0', $schedule->getVersion());
        $this->assertEquals($conference, $schedule->getConference());
        $this->assertEquals($days, $schedule->getDay());
    }

    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getRooms
     */
    public function testGetRooms()
    {
        $conference = new Conference('Test', 'T');
        $room1 = new Room('Test 1');
        $room2 = new Room('Test 2');
        $room3 = new Room('Test 3');
        $days = [
            new Day(
                '2042-01-01',
                new Carbon('2042-01-01T00:00:00+00:00'),
                new Carbon('2042-01-01T23:59:00+00:00'),
                1,
                [$room1, $room2]
            ),
            new Day(
                '2042-01-02',
                new Carbon('2042-02-01T00:00:00+00:00'),
                new Carbon('2042-02-01T23:59:00+00:00'),
                2,
                [new Room('Test 2'), $room3]
            ),
        ];
        $schedule = new Schedule('Lorem 1.3.3.7', $conference, $days);

        $this->assertEquals(['Test 1' => $room1, 'Test 2' => $room2, 'Test 3' => $room3], $schedule->getRooms());

        $schedule = new Schedule('Lorem 1.3.3.0', $conference, []);
        $this->assertEquals([], $schedule->getRooms());
    }

    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getStartDateTime
     * @covers \Handtuchsystem\Helpers\Schedule\Schedule::getEndDateTime
     */
    public function testGetDateTimes()
    {
        $conference = new Conference('Some Conference', 'SC');
        $days = [
            new Day(
                '2042-01-02',
                new Carbon('2042-01-02T00:00:00+00:00'),
                new Carbon('2042-01-02T23:59:00+00:00'),
                2
            ),
            new Day(
                '2042-01-01',
                new Carbon('2042-01-01T00:00:00+00:00'),
                new Carbon('2042-01-01T23:59:00+00:00'),
                1
            ),
            new Day(
                '2042-01-04',
                new Carbon('2042-01-04T00:00:00+00:00'),
                new Carbon('2042-01-04T23:59:00+00:00'),
                3
            ),
        ];
        $schedule = new Schedule('Ipsum tester', $conference, $days);

        $this->assertEquals('2042-01-01T00:00:00+00:00', $schedule->getStartDateTime()->format(Carbon::RFC3339));
        $this->assertEquals('2042-01-04T23:59:00+00:00', $schedule->getEndDateTime()->format(Carbon::RFC3339));

        $schedule = new Schedule('Ipsum old', $conference, []);
        $this->assertNull($schedule->getStartDateTime());
        $this->assertNull($schedule->getEndDateTime());
    }

    /**
     * Prepare test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
    }
}
