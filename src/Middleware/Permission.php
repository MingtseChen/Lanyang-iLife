<?php

class Permission
{
    private $session;
    private $method;

    public function __construct($user)
    {
        $this->session = new \SlimSession\Helper;
        $this->method = $user;
    }

    public function __invoke($request, $response, $next)
    {
        $isAllow = false;
        $group = $this->session->group;
        if ($group == -1) {
            $isAllow = false;
        } elseif ($group == 0) {
            $isAllow = true;
        } else {
            if ($this->method == 'bus') {
                if ($group == 1) {
                    $isAllow = true;
                }
            } elseif ($this->method == 'package') {
                if ($group == 2) {
                    $isAllow = true;
                }
            } elseif ($this->method == 'repair') {
                if ($group == 3) {
                    $isAllow = true;
                }
            } else {
                $isAllow = false;
            }
        }
        if ($isAllow) {
            $response = $next($request, $response);
            return $response;
        } else {
            return $response->withRedirect('/', 403);
        }
    }
}