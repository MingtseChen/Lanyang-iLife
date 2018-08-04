<?php

use Carbon\Carbon;

class Package
{
    private $flash;

    public function __construct()
    {
    }

    public function readUserUnpickedPackage($name)
    {
        $packages = ORM::forTable('package_info')->where(['recipients' => $name, 'is_pick' => 0])->findArray();
        return $packages;
    }

    public function readUserPickedPackage($name)
    {
        $packages = ORM::forTable('package_info')->where(['recipients' => $name, 'is_pick' => 1])->findArray();
        return $packages;
    }

    public function lostFoundPackage()
    {
        $packages = ORM::forTable('package_info')->where(['is_pick' => 0, 'lost_found' => 1])->findArray();
        return $packages;
    }

    public function signPackage($name, $id)
    {
        $packages = ORM::forTable('package_info')->where(['recipients' => $name, 'id' => $id])->findOne();
        if ($packages == false) {
            return false;
        } else {
            $packages->is_pick = true;
            $packages->timestamp_pickup = Carbon::now();
            $packages->save();
            return true;
        }

    }

}