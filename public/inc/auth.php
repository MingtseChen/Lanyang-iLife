<?php

/**
 *
 */
class Auth
{
    private $headers = NULL;

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

    }

    public function sso_userid()
    {
        return $this->headers["sso_userid"];
    }

    public function sso_roletype()
    {

        return $this->headers["sso_roletype"];
    }

    public function requestLogin()
    {

    }
    public function isLogin()
    {
        if(is_null($this->sso_userid()))
            return false;
        else if (!is_null($this->sso_userid()))
            return true;
        else
            return false;
    }
}
