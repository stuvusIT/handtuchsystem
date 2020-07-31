<?php

namespace Handtuchsystem\Migrations;

use Handtuchsystem\Database\Migration\Migration;
use Handtuchsystem\Models\User\State;

class FixMissingArrivalDates extends Migration
{
    /**
     * Run the migration
     */
    public function up()
    {
        $states = State::whereArrived(true)->whereArrivalDate(null)->get();
        foreach ($states as $state) {
            $state->arrival_date = $state->user->personalData->planned_arrival_date;
            $state->save();
        }
    }

    /**
     * Down is not possible and not needed since this is a bugfix.
     */
    public function down()
    {
    }
}
