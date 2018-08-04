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

    public function getSchedule()
    {
        $bus = ORM::forTable('bus_schedule')->findArray();
        return $bus;
    }

    public function delete($id)
    {
        $bus = ORM::forTable('bus_schedule')->findOne($id);
        $bus->delete();
    }

    //use this function for secure
    public function deleteReserve($busID, $userID)
    {
        $bus = ORM::forTable('bus_reserve')->where(['uid' => $userID, 'bus_id' => $busID])->findOne();
        if ($bus == false) {
            return false;
        } else {
            $bus->delete();
            return true;
        }
    }

    public function deleteUser($id)
    {
        $bus = ORM::forTable('bus_reserve')->findOne($id);
        $bus->delete();
    }

    public function edit($data)
    {
        $bus = ORM::forTable('bus_schedule')->findOne($data['id']);
        $bus->type = $data['dest'];
        $bus->departure_time = $data['depart'];
        $bus->reservation = $data['reserve'];
        $bus->capacity = $data['capacity'];
        $bus->description = $data['desc'];
        $bus->save();
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
        $capacity = ORM::forTable('bus_schedule')->select('capacity')->where('id', $busId)->find_array();
        $reserveCount = ORM::forTable('bus_reserve')->where('id', $busId)->count();
        $duplicate = ORM::forTable('bus_reserve')->where(['bus_id' => $busId, 'uid' => $uid])->count();

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
        $suspend = ORM::forTable('bus_suspend')->where('uid', $uid)->count();
        if ($suspend != 0) {
            $this->flash->addMessage('error', 'This account has been suspended');
            return true;
        } else {
            return false;
        }
    }

    private function create($busId, $uid, $name, $dept, $room)
    {
        $user = ORM::forTable('bus_reserve')->create();
        $user->bus_id = $busId;
        $user->uid = $uid;
        $user->username = $name;
        $user->department = $dept;
        $user->dorm_no = $room;
        $user->save();
    }

    public function adminCreate($data)
    {
        $bus = ORM::forTable('bus_schedule')->create();
        $bus->type = $data['dest'];
        $bus->departure_time = Carbon::parse($data['depart']);
        $bus->reservation = $data['reserve'];
        $bus->capacity = $data['capacity'];
        $bus->description = $data['desc'];
        $bus->save();
    }

    public function showReserveUser($id)
    {
        $reserve = ORM::forTable('bus_reserve')->where('bus_id', $id)->findArray();
        return $reserve;
    }

    public function createSuspendList($data)
    {
        $suspend = ORM::forTable('bus_suspend')->create();
        $suspend->uid = $data["uid"];
        $suspend->description = $data["desc"];
        $suspend->save();
    }

    public function readSuspendList()
    {
        $sql = ORM::forTable('bus_suspend')->innerJoin('students', ["bus_suspend.uid", "=", "students.uname"]);
        $user = $sql->selectMany(["bus_suspend.*", "students.name"])->findArray();
        return $user;
    }

    public function deleteSuspendList($id)
    {
        $sql = ORM::forTable('bus_suspend')->findOne($id);
        $sql->delete();
    }

    public function updateSuspendList($data)
    {
        $suspend = ORM::forTable('bus_suspend')->findOne($data['id']);
        $suspend->uid = $data["uid"];
        $suspend->description = $data["desc"];
        $suspend->save();
    }

    public function readUserBus($uid)
    {
        $column = [
            'bus_schedule.*',
            'bus_reserve.bus_id',
            'bus_reserve.uid',
            'bus_reserve.create_time'
        ];
        $select = ORM::forTable('bus_schedule')->selectMany($column);
        $rule = ['bus_schedule.id', '=', 'bus_reserve.bus_id'];
        $today = Carbon::today();
        $buses = $select->join('bus_reserve', $rule)->where('bus_reserve.uid', $uid)->where_gte('departure_time',
            $today)->findArray();
        return $buses;
    }

}