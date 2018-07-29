<?php

use Carbon\Carbon;

class Bus
{
    private $flash;

    public function __construct()
    {
        Carbon::now('Asia/Taipei');
        $this->flash = new \Slim\Flash\Messages();
    }

    public function find($from, $date)
    {
        $setStartDay = Carbon::parse($date);
        $setEndDay = Carbon::parse($date)->addHours(24);
        $query = "`type` = " . $from . " AND `departure_time` BETWEEN '" . $setStartDay . "' AND '" . $setEndDay . "'";
        $schedule = ORM::forTable('bus_schedule')->whereRaw($query)->findArray();
        return $schedule;
    }

    public function reserve($busId, $uid, $name, $dept, $room)
    {
        if ($this->checkBusStatus($busId, $uid)) {
            $this->create($busId, $uid, $name, $dept, $room);
            return true;
        } else {
            return false;
        }

    }

    public function checkBusStatus($busId, $uid)
    {
        $capacity = ORM::for_table('bus_schedule')->select('capacity')->where('id', $busId)->find_array();
        $reserveCount = ORM::for_table('bus_reserve')->where('id', $busId)->count();
        $duplicate = ORM::for_table('bus_reserve')->where(['bus_id' => $busId, 'uid' => $uid])->count();

        if ($duplicate > 1 || $this->isSuspend($uid)) {
            $this->flash->addMessage('error', 'You have already reserve this bus');
            return false;
        } else {
            if ((int)$capacity[0] > $reserveCount) {
                return true;
            } else {
                return false;
            }
        }

    }

    public function isSuspend($uid)
    {
        $suspend = ORM::for_table('bus_suspend')->where('uid', $uid)->count();
        if ($suspend != 0) {
            $this->flash->addMessage('error', 'This account has been suspended');
            return true;
        } else {
            return false;
        }
    }

    private function create($busId, $uid, $name, $dept, $room)
    {
        $user = ORM::for_table('bus_reserve')->create();
        $user->bus_id = $busId;
        $user->uid = $uid;
        $user->username = $name;
        $user->department = $dept;
        $user->dorm_no = $room;
        $user->save();
    }
}