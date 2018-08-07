<?php

use Carbon\Carbon;

class SSO
{
    public $session;

    public function __construct()
    {
        $this->session = new SlimSession\Helper();
    }

    public function __invoke($request, $response, $next)
    {
        $uid = '';
        $ssoRole = '';
        $headers = apache_request_headers();
        $ssoLogin = array_key_exists("sso_userid", $headers);
        $ssoLogin = true;


        //*****************************************//test only
        $uri = $request->getUri();
        $host = $uri->getHost();

        if ($host == "localhost") {
            $uid = 403840308;
            $ssoRole = 1;
            $headers['sso_userid'] = $uid;
            $headers['sso_roletype'] = $ssoRole;
        }
        //*****************************************//test only

        //retrieve value from sso
        if ($ssoLogin) {
            $uid = $headers['sso_userid'];
            $ssoRole = $headers['sso_roletype'];
            $name = $this->username($uid);
            if (!isset($_SESSION['id']) || $this->isIDChange($uid)) {
                if ($this->isAdmin($uid)) {
                    $group = $this->userGroup($uid);
                    $this->setAdminSession($uid, $name, $ssoRole, $group);
                    $this->updateAdminLogin($uid);
                } else {
                    $this->setUserSession($uid, $name, $ssoRole);
                    $this->updateUserLogin($uid);
                }
            }
        }
        $response = $next($request, $response);
        //after response area

//        //retrieve value from sso
//        if ($ssoLogin) {
//            $uid = $headers['sso_userid'];
//            $ssoRole = $headers['sso_roletype'];
//            $name = $this->username($uid);
//            if (!isset($_SESSION['id']) || $this->isIDChange($uid)) {
//                if ($this->isAdmin($uid)) {
//                    $group = $this->userGroup($uid);
//                    $this->setAdminSession($uid, $name, $ssoRole, $group);
//                    $this->updateAdminLogin($uid);
//                } else {
//                    $this->setUserSession($uid, $name, $ssoRole);
//                    $this->updateUserLogin($uid);
//                }
//            }
//        }

//        if ($ssoLogin) {
//            $uid = $headers['sso_userid'];
//            $ssoRole = $headers['sso_roletype'];
//            $name = $this->username($uid);
//            if (!isset($session['id']) || $this->isIDChange($uid)) {
//                if ($this->isAdmin($uid)) {
//                    $group = $this->userGroup($uid);
//                    $this->setAdminSession($uid, $name, $ssoRole, $group);
//                    $this->updateAdminLogin($uid);
//                } else {
//                    $this->setUserSession($uid, $name, $ssoRole);
//                    $this->updateUserLogin($uid);
//                }
//            }
//        }

//        if (!isset($session['id']) || $this->isIDChange($uid)) {
//            if ($this->isAdmin($uid)) {
//                $group = $this->userGroup($uid);
//                $this->setAdminSession($uid, $name, $ssoRole, $group);
//                $this->updateAdminLogin($uid);
//            } else {
//                $this->setUserSession($uid, $name, $ssoRole);
//                $this->updateUserLogin($uid);
//            }
//        }

        return $response;
    }

    public function username($uid)
    {
        $table = ORM::forTable('students')->selectMany(['uname', 'name']);
        $data = $table->where(['uname' => $uid])->findArray();
        if (empty($data)) {
            return 'user not found in database';
        }
        $name = trim($data[0]['name'], '　');
        return $name;
    }

    public function isIDChange($newID)
    {
        $oldID = $this->session->id;
        if ($newID != $oldID) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin($uid)
    {
        $data = ORM::forTable('groups_users')->where('uid', $uid)->count();
        if ($data != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function userGroup($uid)
    {
        $table = ORM::forTable('groups_users')->select(['uid', 'role']);
        $data = $table->where(['uid' => $uid])->findArray();
        $group = $data[0]['role'];
        return $group;
    }

    public function setAdminSession($uid, $name, $ssoRole, $group)
    {
        $this->session->set('id', $uid);
        $this->session->set('name', trim($name, '　'));
        $this->session->set('ssoRole', $ssoRole);
        $this->session->set('group', $group);
    }

    public function updateAdminLogin($uid)
    {
        $now = Carbon::now();
        $record = ORM::for_table('groups_users')->where('uid', $uid)->findOne();
        $record->set('last_login', $now);
        $record->save();
    }

    public function setUserSession($uid, $name, $ssoRole)
    {
        $this->session->set('id', $uid);
        $this->session->set('name', trim($name, '　'));
        $this->session->set('ssoRole', $ssoRole);
    }

    public function updateUserLogin($uid)
    {
        $now = Carbon::now();
        $record = ORM::for_table('students')->where('uname', $uid)->findOne();
        $record->set('last_login', $now);
        $record->save();
    }
}
