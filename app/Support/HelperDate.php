<?php

namespace App\Support;

use Carbon\Carbon;

class HelperDate 
{
    /**
     * Get time diff between two moment
     */
    public static function getDiffTime($end, $start)
    {
        $start = new Carbon($start);
        $end = new Carbon($end);

        return $end->diffInMinutes($start);
    }

    /**
     * Get all dates in a range
     */
    public static function generateDateRange(Carbon $start_date, Carbon $end_date, $format = 'Y-m-d')
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format($format);
        }

        return $dates;
    }
}