<?php

//namespace App\Models;

class User
{

    public function create($dataArray)
    {
        $user = ORM::for_table('user_group')->create();
        $user->uid = $dataArray['uid'];
        $user->active = $dataArray['active'];
        $user->role = $dataArray['role'];
//        $user->create_time = Carbon::now();
        $user->save();
    }

    public function usernameIsValid($uname)
    {
        $user = ORM::for_table('user_group')->select('uid')->where('uid', $uname['uid'])->count();
        if ($user > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function readAdminRoleList()
    {
        $list = ORM::forTable('user_role')->findArray();
        return $list;
    }

    public function updateAdmin($id, $active, $role)
    {
        $user = ORM::forTable('user_group')->findOne($id);
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
        $user = ORM::forTable('user_group')->findOne($id);
        if ($user == false) {
            return false;
        } else {
            $user->delete();
            return true;
        }
    }

    public function read()
    {
        $table = ORM::forTable('user_group');
        $row = $table->selectMany('user_group.id', 'user_group.uid', 'active', 'role', 'create_time',
            'user_group.last_login', 'students.name');
        $data = $row->leftOuterJoin('students', array('user_group.uid', '=', 'students.uname'))->find_array();
        foreach ($data as $key => $value) {
            $role = $data[$key]['role'];
            $data[$key]['role_name'] = $this->getRole($role);
        }
        return $data;
    }

    public function getRole($role)
    {
        $role = ORM::forTable('user_role')->select('name')->findOne($role);
        return $role['name'];
    }

    public function statistic()
    {
        $studentCount = ORM::forTable('students')->count();
        $userCount = ORM::forTable('user_group')->count();
        $pendingUser = ORM::forTable('user_group')->count();
        $activeUser = ORM::forTable('user_group')->where('active', true)->count();
        return [$studentCount, $userCount, $pendingUser, $activeUser];
    }
}
