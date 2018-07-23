<?php

namespace App;


//TODO Fix sso and user login conflict
class Auth
{
    private $headers = null;
    private $session;

    function __construct()
    {
        $this->headers = apache_request_headers();
        $this->session = new \SlimSession\Helper;
    }

    public function logout()
    {
        // code...
    }

    public function ssoRoleType()
    {
        return $this->headers["sso_roletype"];
    }

    public function isSsoLogin()
    {
        //sso check
        if (is_null($this->ssoUserId())) {
            return false;
        } else {
            return true;
        }
    }

    public function ssoUserId()
    {
        if (array_key_exists("sso_userid", $this->headers)) {
            return $this->headers["sso_userid"];
        } else {
            return null;
        }
    }

    public function loginType()
    {
        $type = null;

        if (!is_null($this->ssoUserId())) {
            $type = 'sso';
        }
        if (isset($this->session['uname'])) {
            $type = 'user';
        }
        return $type;
    }

    public function loginFix()
    {
        $type = null;
        if (!is_null($this->ssoUserId())) {
            $type = 'sso';
        }
        if (isset($this->session['uname'])) {
            $type = 'user';
        }
        return $type;
    }

    public function isUserLogin()
    {
        $isUserLogin = isset($this->session['uname']);
        //user check
        if ($isUserLogin) {
            return true;
        } else {
            return false;
        }
    }
}
