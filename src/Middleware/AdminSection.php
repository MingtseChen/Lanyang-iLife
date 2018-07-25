<?php

class AdminSection
{
    private $session;
    private $flash;

    public function __construct()
    {
        $this->session = new \SlimSession\Helper;

    }

    public function __invoke($request, $response, $next)
    {
        $this->flash = new \Slim\Flash\Messages();
        $isLogin = isset($this->session['uname']);
        if (!$isLogin) {
            $this->flash->addMessage('hi', 'sssss');
            return $response->withRedirect('/login');
        }
        $response = $next($request, $response);
        return $response;
    }
}