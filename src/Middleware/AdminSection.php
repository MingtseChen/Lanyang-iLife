<?php

class AdminSection
{
    private $session;
    private $auth;

    public function __construct($app)
    {
        $this->session = new \SlimSession\Helper;
        $this->auth = $app->auth;
    }

    public function __invoke($request, $response, $next)
    {
        $ssoLoginCheck = $auth->isSsoLogin();
        $response = $next($request, $response);
        return $response;
    }
}