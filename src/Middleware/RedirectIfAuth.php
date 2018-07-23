<?php

class RedirectIfAuth
{
    private $session;

    public function __construct()
    {
        $this->session = new \SlimSession\Helper;
    }

    public function __invoke($request, $response, $next)
    {
        $isLogin = isset($this->session['uname']);
        if ($isLogin) {
            return $response->withRedirect('/');
        }
        $response = $next($request, $response);
        return $response;
    }
}