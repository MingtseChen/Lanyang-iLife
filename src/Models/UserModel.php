<?php

//namespace App\Models;

class User
{

    public function create($dataArray)
    {
        $user = ORM::for_table('users')->create();

        $user->name = $dataArray['name'];
        $user->uname = $dataArray['username'];
        $user->email = $dataArray['email'];
        $user->pass = password_hash($dataArray['pwd'], PASSWORD_BCRYPT);
        $user->active = $dataArray['active'];
        $user->role = $dataArray['role'];
        $user->save();
    }

    public function usernameIsValid($uname)
    {
        $user = ORM::for_table('users')->select('uname')->where('uname', $uname['username'])->count();
        if ($user > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function read()
    {
        return ORM::forTable('users')->find_array();
    }

    public function statistic()
    {
        $studentCount = ORM::forTable('students')->count();
        $userCount = ORM::forTable('users')->count();
        $pendingUser = ORM::forTable('users')->count();
        $activeUser = ORM::forTable('users')->where('active', true)->count();
        return [$studentCount, $userCount, $pendingUser, $activeUser];
    }

    public function login($loginData)
    {
        $uname = $loginData['uname'];
        $password = $loginData['pwd'];
        if ($uname == '' || $password == '') {
            return false;
        }
        if ($this->hasUser($uname)) {
            $verify = password_verify($password, $this->getPassword($uname));
            if ($verify) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    public function hasUser($uname)
    {
        $rowCount = ORM::forTable('users')->where('uname', $uname)->count();
        if ($rowCount == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getPassword($uname)
    {
        $row = ORM::forTable('users')->where('uname', $uname)->findOne();
        return $row->pass;
    }
}
