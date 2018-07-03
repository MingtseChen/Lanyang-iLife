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
        $user = $this->headers["sso_userid"];
        if (!is_null($user))
            return $this->headers["sso_userid"];
        else
            return null;
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
        if (is_null($this->sso_userid()))
            return false;
        else
            return true;
    }
}
