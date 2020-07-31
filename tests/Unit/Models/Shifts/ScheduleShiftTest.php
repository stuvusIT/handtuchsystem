<?php

namespace Handtuchsystem\Test\Unit\Models\Shifts;

use Handtuchsystem\Models\Shifts\Schedule;
use Handtuchsystem\Models\Shifts\ScheduleShift;
use Handtuchsystem\Test\Unit\Models\ModelTest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleShiftTest extends ModelTest
{
    /**
     * @covers \Handtuchsystem\Models\Shifts\ScheduleShift::schedule
     */
    public function testScheduleShifts()
    {
        $schedule = new Schedule(['url' => 'https://lorem.ipsum/schedule.xml']);
        $schedule->save();

        $scheduleShift = new ScheduleShift(['shift_id' => 1, 'guid' => 'a']);
        $scheduleShift->schedule()->associate($schedule);
        $scheduleShift->save();

        /** @var ScheduleShift $scheduleShift */
        $scheduleShift = (new ScheduleShift())->find(1);
        $this->assertInstanceOf(BelongsTo::class, $scheduleShift->schedule());
        $this->assertEquals($schedule->id, $scheduleShift->schedule->id);
    }
}
