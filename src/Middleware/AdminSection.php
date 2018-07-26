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
        $response = $next($request, $response);
        return $response;
    }
}