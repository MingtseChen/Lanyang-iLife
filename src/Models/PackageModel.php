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

    public function lostFoundPackageRead()
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

    public function unsignPackage($name, $id)
    {
        $packages = ORM::forTable('package_info')->where(['recipients' => $name, 'id' => $id])->findOne();
        if ($packages == false) {
            return false;
        } else {
            $packages->is_pick = false;
            $packages->save();
            return true;
        }

    }

    public function lostFoundPackage($id)
    {
        $packages = ORM::forTable('package_info')->where('id', $id)->findOne();
        if ($packages == false) {
            return false;
        } else {
            $packages->lost_found = true;
            $packages->save();
            return true;
        }

    }

    public function lostFoundUndoPackage($id)
    {
        $packages = ORM::forTable('package_info')->where('id', $id)->findOne();
        if ($packages == false) {
            return false;
        } else {
            $packages->lost_found = false;
            $packages->save();
            return true;
        }

    }

    /**
     * @return mixed
     */
    public function readAllUnpickPackage()
    {
        $packages = ORM::forTable('package_info')->where('is_pick', false)->findArray();
        return $packages;
    }

    public function readHistoryPackage()
    {
        $packages = ORM::forTable('package_info')->where('is_pick', true)->findArray();
        return $packages;
    }

    public function updatePackage($id, $rcp, $cat, $strg, $pid, $time)
    {
        try {
            $packageDetail = ORM::forTable('package_info')->findOne($id);
            if ($packageDetail == false) {
                return false;
            }
            $packageDetail->recipients = $rcp;
            $packageDetail->ptype = $cat;
            $packageDetail->storage = $strg;
            $packageDetail->pid = $pid;
            $packageDetail->timestamp_arrive = $time;
            $packageDetail->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createPackage($rcp, $cat, $strg, $pid, $time)
    {
        try {
            $packageDetail = ORM::forTable('package_info')->create();
            $packageDetail->recipients = $rcp;
            $packageDetail->ptype = $cat;
            $packageDetail->storage = $strg;
            $packageDetail->pid = $pid;
            $packageDetail->timestamp_arrive = $time;
            $packageDetail->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deletePackage($id)
    {
        try {
            $packageDetail = ORM::forTable('package_info')->findOne($id);
            if ($packageDetail == false) {
                return false;
            }
            $packageDetail->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}