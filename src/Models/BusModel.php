<?php

use Carbon\Carbon;

class Bus
{
    public function getWeekSchedule()
    {
        //sunday will be the first day of the week
        //add 10 minute for the policy 'can not reserve seats 60 min before departure'
        $now = Carbon::now('Asia/Taipei')->addMinutes(60);
        $endOfWeek = Carbon::now('Asia/Taipei')->endOfWeek();
        $whereClause = 'departure_time BETWEEN \'' . $now . '\' AND \'' . $endOfWeek . '\'';
        //find schedule in given range
        $time = ORM::forTable('bus_schedule')->where_raw($whereClause);
        //find opened schedule
        $buses = $time->whereGte('departure_time', $now)->findArray();
        foreach ($buses as $key => $bus) {
            $id = $buses[$key]['id'];
            $buses[$key]['reserved_seats'] = $this->getRemainSeats($id);
            if ((int)$buses[$key]['capacity'] - (int)$this->getRemainSeats($id) == 0) {
                $buses[$key]['can_reserve'] = false;
            }
            if ((int)$buses[$key]['capacity'] - (int)$this->getRemainSeats($id) == 0) {
                $buses[$key]['can_reserve'] = true;
            }

        }
        return $buses;
    }

    public function getRemainSeats($id)
    {
        $count = ORM::forTable('bus_reserve')->where(['bus_id' => $id])->count();
        return $count;
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

//    public function find($from, $date)
//    {
//        try {
//            $setStartDay = Carbon::parse($date);
//            $setEndDay = Carbon::parse($date)->addHours(24);
//            if ($setStartDay->isToday()) {
//                $setStartDay = Carbon::now();
//            }
//            $query = "`type` = " . $from . " AND `departure_time` BETWEEN '" . $setStartDay . "' AND '" . $setEndDay . "'";
//            $schedule = ORM::forTable('bus_schedule')->whereRaw($query)->findArray();
//            return $schedule;
//        } catch (Exception $e) {
//            var_dump($e);
//            return false;
//        }
//
//    }

    public function reserve($uid, $name, $busId)
    {
        if ($this->checkBusStatus($busId, $uid)) {
            $this->create($uid, $name, $busId);
            return true;
        } else {
            return false;
        }

    }

    public function checkBusStatus($busId, $uid)
    {
        $capacity = ORM::forTable('bus_schedule')->select('capacity')->where('id', $busId)->find_array();
        $reserveCount = ORM::forTable('bus_reserve')->where('bus_id', $busId)->count();
        $duplicate = ORM::forTable('bus_reserve')->where(['bus_id' => $busId, 'uid' => $uid])->count();

        if ($duplicate > 1 || $this->isSuspend($uid)) {
//            $this->flash->addMessage('error', 'You have already reserve this bus');
            return false;
        } else {
            $capacity = (int)$capacity[0]['capacity'];
            if ($capacity > $reserveCount) {
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
//            $this->flash->addMessage('error', 'This account has been suspended');
            return true;
        } else {
            return false;
        }
    }

    private function create($uid, $name, $busId)
    {
        $user = ORM::forTable('bus_reserve')->create();
        $user->bus_id = $busId;
        $user->uid = $uid;
        $user->username = $name;
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