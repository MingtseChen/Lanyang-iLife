<?php

class RedirectIfAuth
{
    private $session;

//    private $message;

    public function __construct()
    {
        $this->session = new SlimSession\Helper;
//        $this->message = new Slim\Flash\Messages();
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