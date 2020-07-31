<?php

namespace Handtuchsystem\Test\Unit\Models\Shifts;

use Handtuchsystem\Models\Shifts\Schedule;
use Handtuchsystem\Models\Shifts\ScheduleShift;
use Handtuchsystem\Test\Unit\Models\ModelTest;

class ScheduleTest extends ModelTest
{
    /**
     * @covers \Handtuchsystem\Models\Shifts\Schedule::scheduleShifts
     */
    public function testScheduleShifts()
    {
        $schedule = new Schedule(['url' => 'https://foo.bar/schedule.xml']);
        $schedule->save();

        (new ScheduleShift(['shift_id' => 1, 'schedule_id' => $schedule->id, 'guid' => 'a']))->save();
        (new ScheduleShift(['shift_id' => 2, 'schedule_id' => $schedule->id, 'guid' => 'b']))->save();
        (new ScheduleShift(['shift_id' => 3, 'schedule_id' => $schedule->id, 'guid' => 'c']))->save();

        $this->assertCount(3, $schedule->scheduleShifts);
    }
}
