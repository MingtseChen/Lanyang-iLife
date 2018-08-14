<?php

class Repair
{
    public function readCategory()
    {
        $category = ORM::forTable('repair_cat')->orderByAsc('id')->findArray();
        return $category;
    }

    public function readBuilding()
    {
        $building = ORM::forTable('repair_building')->orderByAsc('id')->findArray();
        return $building;
    }

    public function createRepair($id, $building, $room, $item_cat, $item, $desc, $accompany, $confirm,$filename)
    {
        try {
            $repairItem = ORM::forTable('repair_item')->create();
            $repairItem->building = $building;
            $repairItem->note_man = $id;
            $repairItem->location = $room;
            $repairItem->item_cat = $item_cat;
            $repairItem->item = $item;
            $repairItem->item_desc = $desc;
            $repairItem->request_need_accompany = $accompany;
            $repairItem->request_need_confirm = $confirm;
            $repairItem->photo = $filename;
            $repairItem->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}