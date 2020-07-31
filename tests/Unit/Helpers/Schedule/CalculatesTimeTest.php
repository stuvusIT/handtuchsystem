<?php

namespace Handtuchsystem\Test\Unit\Helpers\Schedule;

use Handtuchsystem\Helpers\Schedule\CalculatesTime;
use Handtuchsystem\Test\Unit\TestCase;

class CalculatesTimeTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Helpers\Schedule\CalculatesTime::secondsFromTime
     */
    public function testSecondsFromTime()
    {
        $calc = new class {
            use CalculatesTime;

            /**
             * @param string $time
             * @return int
             */
            public function calc(string $time): int
            {
                return $this->secondsFromTime($time);
            }
        };

        $this->assertEquals(0, $calc->calc('0:00'));
        $this->assertEquals(60, $calc->calc('0:01'));
        $this->assertEquals(60 * 60, $calc->calc('01:00'));
        $this->assertEquals(60 * 60 * 10 + 60 * 11, $calc->calc('10:11'));
    }
}
