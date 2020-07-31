<?php

namespace Handtuchsystem\Test\Unit\Helpers\Schedule;

use Handtuchsystem\Helpers\Schedule\Conference;
use Handtuchsystem\Test\Unit\TestCase;

class ConferenceTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::__construct
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getTitle
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getAcronym
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getStart
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getEnd
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getDays
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getTimeslotDuration
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getTimeslotDurationSeconds
     * @covers \Handtuchsystem\Helpers\Schedule\Conference::getBaseUrl
     */
    public function testCreate()
    {
        $conference = new Conference('Doing stuff', 'DS');
        $this->assertEquals('Doing stuff', $conference->getTitle());
        $this->assertEquals('DS', $conference->getAcronym());
        $this->assertNull($conference->getStart());
        $this->assertNull($conference->getEnd());
        $this->assertNull($conference->getDays());
        $this->assertNull($conference->getTimeslotDuration());
        $this->assertNull($conference->getTimeslotDurationSeconds());
        $this->assertNull($conference->getBaseUrl());

        $conference = new Conference(
            'Doing stuff',
            'DS',
            '2042-01-01',
            '2042-01-10',
            10,
            '00:10',
            'https://foo.bar/schedule'
        );
        $this->assertEquals('2042-01-01', $conference->getStart());
        $this->assertEquals('2042-01-10', $conference->getEnd());
        $this->assertEquals(10, $conference->getDays());
        $this->assertEquals('00:10', $conference->getTimeslotDuration());
        $this->assertEquals(60 * 10, $conference->getTimeslotDurationSeconds());
        $this->assertEquals('https://foo.bar/schedule', $conference->getBaseUrl());
    }
}
