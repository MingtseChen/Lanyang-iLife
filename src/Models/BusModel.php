<?php

use Carbon\Carbon;

class Bus
{
    public function getWeekSchedule()
    {
        //sunday will be the first day of the week
        //add 10 minute for the policy 'can not reserve seats 60 min before departure'
        $now = Carbon::now('Asia/Taipei');
//        $now = Carbon::parse("2018-09-03 00:00");
        $endOfWeek = Carbon::now('Asia/Taipei')->endOfWeek();
        $whereClause = 'departure_time BETWEEN \'' . $now . '\' AND \'' . $endOfWeek . '\'';
        //find schedule in given range
        $time = ORM::forTable('bus_schedule')->where_raw($whereClause);
        //find opened schedule
        $buses = $time->whereGte('departure_time', $now)->orderByAsc('departure_time')->findArray();
        foreach ($buses as $key => $bus) {
            $id = $buses[$key]['id'];
            $remain = (int)$buses[$key]['capacity'] - (int)$this->getRemainSeats($id);
            $buses[$key]['reserved_seats'] = $this->getRemainSeats($id);

            if ($this->isOpen($id) && $remain > 0) {
                $buses[$key]['is_open'] = true;
            } else {
                $buses[$key]['is_open'] = false;
            }

        }
        return $buses;
    }

    public function getRemainSeats($id)
    {
        $count = ORM::forTable('bus_reserve')->where(['bus_id' => $id])->count();
        return $count;
    }

    public function isOpen($busID)
    {
        $col = ORM::forTable('bus_schedule')->select('open_time')->findOne($busID);
        $openTime = Carbon::parse($col->open_time);
        if (Carbon::now()->lessThan($openTime)) {
            return false;
        } else {
            return true;
        }
    }

    public function getSchedule()
    {
        $buses = ORM::forTable('bus_schedule')->findArray();
        foreach ($buses as $key => $bus) {
            $id = $buses[$key]['id'];
            $buses[$key]['reserved_seats'] = $this->getRemainSeats($id);

        }
        return $buses;
    }

    //use this function for security reason

    public function delete($id)
    {
        $bus = ORM::forTable('bus_schedule')->findOne($id);
        $bus->delete();
    }

    public function deleteReserve($busID, $userID)
    {
        $dept_time = $this->checkDeparture($busID);
        if ($this->enableModify($dept_time)) {
            $bus = ORM::forTable('bus_reserve')->where(['uid' => $userID, 'bus_id' => $busID])->findOne();
            if ($bus == false) {
                return false;
            } else {
                $bus->delete();
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkDeparture($busID)
    {
        $time = ORM::forTable('bus_schedule')->select('departure_time')->findOne($busID);
        return $time['departure_time'];
    }

    public function enableModify($time)
    {
        $deadline = Carbon::parse($time)->subHour(1);
        if (Carbon::now()->gte($deadline)) {
            return false;
        } else {
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

    public function reserve($uid, $name, $busId)
    {
        if ($this->checkBusStatus($busId, $uid)) {
            $this->create($uid, $name, $busId);
            return true;
        } else {
            return false;
        }

    }

    public function checkBusStatus($busID, $uid)
    {
        $capacity = ORM::forTable('bus_schedule')->select('capacity')->where('id', $busID)->find_array();
        $reserveCount = ORM::forTable('bus_reserve')->where('bus_id', $busID)->count();
        $duplicate = ORM::forTable('bus_reserve')->where(['bus_id' => $busID, 'uid' => $uid])->count();

        if ($duplicate >= 1 || $this->isSuspend($uid) || !$this->isOpen($busID)) {
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
            $today)->orderByAsc('departure_time')->findArray();
        foreach ($buses as $key => $value) {
            $depart = $buses[$key]['departure_time'];
            if ($this->enableModify($depart)) {
                $buses[$key]['modify'] = true;
            } else {
                $buses[$key]['modify'] = false;
            }
        }
        return $buses;
    }

}