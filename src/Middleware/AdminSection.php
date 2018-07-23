<?php

class AdminSection
{
    private $session;

    public function __construct()
    {
        $this->session = new \SlimSession\Helper;
    }

    public function __invoke($request, $response, $next)
    {
        $isLogin = isset($this->session['uname']);
        if (!$isLogin) {
            return $response->withRedirect('/login');
        }
        $response = $next($request, $response);
        return $response;
    }
}