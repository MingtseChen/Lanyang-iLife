<?php

//namespace App\Models;
use Carbon\Carbon;

class User
{

    public function create($dataArray)
    {
        $user = ORM::for_table('groups_users')->create();
        $user->uid = $dataArray['uid'];
        $user->active = $dataArray['active'];
        $user->role = $dataArray['role'];
//        $user->create_time = Carbon::now();
        $user->save();
    }

    public function usernameIsValid($uname)
    {
        $user = ORM::for_table('groups_users')->select('uid')->where('uid', $uname['uid'])->count();
        if ($user > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function updateAdmin($id, $active, $role)
    {
        $user = ORM::forTable('groups_users')->findOne($id);
        if ($user == false) {
            return false;
        }
        $user->active = $active;
        $user->role = $role;
        $user->save();
        return true;
    }

    public function delete($id)
    {
        $user = ORM::forTable('groups_users')->findOne($id);
        if ($user == false) {
            return false;
        } else {
            $user->delete();
            return true;
        }
    }

    public function read()
    {
        $table = ORM::forTable('groups_users');
        $row = $table->selectMany('groups_users.id', 'groups_users.uid', 'active', 'role', 'create_time',
            'groups_users.last_login', 'students.name');
        $data = $row->leftOuterJoin('students', array('groups_users.uid', '=', 'students.uname'))->find_array();
        return $data;
    }

    public function statistic()
    {
        $studentCount = ORM::forTable('students')->count();
        $userCount = ORM::forTable('groups_users')->count();
        $pendingUser = ORM::forTable('groups_users')->count();
        $activeUser = ORM::forTable('groups_users')->where('active', true)->count();
        return [$studentCount, $userCount, $pendingUser, $activeUser];
    }
}
