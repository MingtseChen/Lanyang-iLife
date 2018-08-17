<?php
//TODO make query more faster(cut down database access)
use Carbon\Carbon;

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

    public function dispatchItem($id, $assign, $assign_notes, $repair_man, $expect_mod)
    {
        $row = ORM::forTable('repair_item')->findOne($id);
        if (!$row) {
            return false;
        }
        $row->assign = $assign;
        $row->item_status = 2;
        $row->assign_notes = $assign_notes;
        $row->assign_time = Carbon::now();
        $row->repair_man = $repair_man;
        $row->expect_time = Carbon::now()->addDays($expect_mod);
        $row->save();
        return true;
    }

    public function finishItem($id, $repair_notes)
    {
        $row = ORM::forTable('repair_item')->findOne($id);
        if (!$row) {
            return false;
        }
        $row->repair_notes = $repair_notes;
        $row->item_status = 3;
        $row->ok_time = Carbon::now();
        $row->save();
        return true;
    }

    public function createRepair($id, $building, $room, $item_cat, $item, $desc, $accompany, $confirm, $filename)
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
            $repairItem->item_calls = 0;
            $repairItem->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function edit($id, $building, $cat)
    {
        $row = ORM::forTable('repair_item')->findOne($id);
        if (!$row) {
            return false;
        }
        $row->building = $building;
        $row->item_cat = $cat;
        $row->save();
        return true;
    }

    public function signWork($id)
    {
        $row = ORM::forTable('repair_item')->findOne($id);
        if (!$row) {
            return false;
        }
        $row->item_status = 1;
        $row->accept_time = Carbon::now();
        $row->accept = true;
        $row->save();
        return true;
    }

    public function showRepairDetail($id)
    {
        $row = ORM::forTable('repair_item')->where('id', $id)->findArray();
        if (empty($row)) {
            return false;
        }
        return $row[0];
    }

    public function readWork($admin, $status)
    {
        $selectClause = ['repair_cat.cat_name', 'repair_cat.admin_id', 'repair_item.*'];
        $joinClause = ['repair_item.item_cat', '=', 'repair_cat.id'];
        $query = ORM::forTable('repair_item')->selectMany($selectClause)->innerJoin('repair_cat', $joinClause);
        $items = $query->where(['item_status' => $status, 'repair_cat.admin_id' => $admin])->findArray();
        foreach ($items as $key => $item) {
            $building = $items[$key]['building'];
            $category = $items[$key]['item_cat'];
            $state = $items[$key]['item_status'];

            $items[$key]['building'] = $this->getBuilding($building);
            $items[$key]['item_cat'] = $this->getCategory($category);
            $items[$key]['item_status_name'] = $this->getItemStatus($state);
        }
        return $items;
    }

    private function getBuilding($value)
    {
        $building = ORM::forTable('repair_building')->select('building_name')->findOne($value);
        return $building['building_name'];
    }

    private function getCategory($value)
    {
        $cat = ORM::forTable('repair_cat')->selectMany('cat_name')->findOne($value);
        return $cat['cat_name'];
    }

    private function getItemStatus($value)
    {
        $building = ORM::forTable('repair_status')->select('status_name')->findOne($value);
        return $building['status_name'];
    }


}