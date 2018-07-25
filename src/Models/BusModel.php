<?php

use Carbon\Carbon;

//namespace App\Models;

class Bus
{
    public function __construct()
    {
        Carbon::now('Asia/Taipei');
    }

    public function find($from, $date)
    {
        $setStartDay = Carbon::parse($date);
        $setEndDay = Carbon::parse($date)->addHours(24);
        $query = "`type` = " . $from . " AND `departure_time` BETWEEN '" . $setStartDay . "' AND '" . $setEndDay . "'";
        $schedule = ORM::forTable('bus_schedule')->whereRaw($query)->find_array();
        return $schedule;
    }
}