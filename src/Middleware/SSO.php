<?php
//include "../../Models/StudentModel.php";
include __DIR__ . "/../auth.php";

class SSO
{
    private $session;
    private $header;

    public function __construct()
    {
        $this->session = new \SlimSession\Helper;
        $this->header = apache_request_headers();
    }

    public function __invoke($request, $response, $next)
    {
        $headers = apache_request_headers();
        $ssoLogin = array_key_exists("sso_userid", $headers);
        if ($ssoLogin) {
            $this->session->set('id', $headers['sso_userid']);
            $this->session->set('role', $headers['sso_roletype']);
        }
        $response = $next($request, $response);
        return $response;
    }
}