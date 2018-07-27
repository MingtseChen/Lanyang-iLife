<?php
//include "../../Models/StudentModel.php";
include __DIR__ . "/../auth.php";

class SSO
{
    public function __invoke($request, $response, $next)
    {
        $session = new \SlimSession\Helper;
        $headers = apache_request_headers();
        $ssoLogin = array_key_exists("sso_userid", $headers);

        $response = $next($request, $response);

        if ($ssoLogin) {
            $session->set('id', $headers['sso_userid']);
            $session->set('role', $headers['sso_roletype']);
        }
        return $response;
    }
}
