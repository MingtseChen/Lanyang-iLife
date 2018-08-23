<?php

class AdminGuard
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
        $group = $this->session->group;
        if ($group == -1) {
            return $response->withRedirect('/', 403);
        }
        $response = $next($request, $response);
        return $response;
    }
}