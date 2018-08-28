<?php

use Carbon\Carbon;

class BusTimeRestriction
{

    public function __invoke($request, $response, $next)
    {
        if ($this->isCloseTime()) {
            return $response->withRedirect('/bus/status?action=fail&status=close');
        }
        $response = $next($request, $response);
        return $response;
    }

    public function isCloseTime()
    {
        $startTime = '00:00:00';
        $endTime = '12:30:00';
        $now = Carbon::now();
        $timeNow = $now->format('H:i:s');
        if ($now->isMonday()) {
            if ($timeNow >= $startTime && $timeNow <= $endTime) {
                return true;
            }
        }
        return false;
    }
}