<?php
//include "../../Models/StudentModel.php";
include __DIR__ . "/../auth.php";

class SSO
{
    private $session;
    private $auth;

    public function __construct()
    {
        $this->session = new \SlimSession\Helper;
        $this->auth = new SSOAuth();
    }

    public function __invoke($request, $response, $next)
    {
        $ssoLoginCheck = $this->auth->isSsoLogin();
        $uri = $request->getUri();
        $host = $uri->getHost();
        if ($host = 'sso.tku.edu.tw') {
            $userId = $request->getHeader('sso_userid');
            $this->session->set('id', $userId);
        }
        $response = $next($request, $response);
        return $response;
    }
}