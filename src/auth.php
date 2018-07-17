<?php

namespace App\User;

class Auth
{
    private $headers = null;

    function __construct()
    {
        $this->headers = apache_request_headers();
    }

    public function login()
    {
        // code...
    }

    public function logout()
    {
        // code...
    }

    public function ssoUserId()
    {
        if (array_key_exists("sso_userid", $this->headers)) {
            return $this->headers["sso_userid"];
        } else {
            return null;
        }
    }

    public function ssoRoleType()
    {

        return $this->headers["sso_roletype"];
    }

    public function requestLogin()
    {
        //code..
    }

    public function isLogin()
    {
        if (is_null($this->ssoUserId())) {
            return false;
        } else {
            return true;
        }
    }
}
