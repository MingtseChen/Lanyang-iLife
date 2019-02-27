<?php
//TODO make query more faster(cut down database access)
use Carbon\Carbon;

class Repair
{
    public function readUserById($id)
    {
        $user = ORM::forTable('repair_item')->select('note_man')->findOne($id);
        return $user;
    }
    public function readCategory()
    {
        $category = ORM::forTable('repair_cat')->orderByAsc('soc')->findArray();
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

    public function getCatUser($cat)
    {
        $item = ORM::forTable('repair_cat')->findOne($cat);
        return $item->admin_mail;
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
        $report = [];
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
            $report = [
                'status' => true,
                'building' => $building,
                'location' => $room,
                'item_cat' => $item_cat,
                'item' => $item,
                'item_desc' => $desc
            ];
            return $report;
        } catch (Exception $e) {
            $report = [
                'status' => false,
                'error' => $e,
            ];
            return $report;
        }
    }

    public function userEditRepair($id, $building, $room, $item_cat, $item, $desc, $accompany, $confirm, $filename)
    {
        try {
            $repairItem = ORM::forTable('repair_item')->findOne($id);
            $repairItem->building = $building;
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

    public function userCall($workID, $desc)
    {
        try {
            $data = ORM::forTable('repair_call')->create();
            $data->call_content = $desc;
            $data->repair_id = $workID;
            $data->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function readWorkSID($workID)
    {
        $id = ORM::forTable('repair_item')->select('note_man')->findOne($workID);
        return $id["note_man"];
    }

    public function userConfirm($id, $uid, $confirmNotes, $evaluation, $evaluationNotes)
    {
        try {
            $repairItem = ORM::forTable('repair_item')->where('note_man', $uid)->findOne($id);
            $repairItem->evaluation = $evaluation;
            $repairItem->evaluation_notes = $evaluationNotes;
            $repairItem->confirm_time = Carbon::now();
            $repairItem->confirm_notes = $confirmNotes;
            $repairItem->confirm = true;
            $repairItem->item_status = 4;
            $repairItem->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getItemUserID($itemID)
    {
        $data = ORM::forTable('repair_item')->select('note_man')->findOne($itemID);
        if (!$data) {
            return false;
        }
        return $data['note_man'];
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

    public function readUserWork($uid, $workID = null)
    {
        $selectClause = ['repair_cat.*', 'repair_item.*'];
        $joinClause = ['repair_item.item_cat', '=', 'repair_cat.id'];
        $query = ORM::forTable('repair_item')->selectMany($selectClause)->innerJoin('repair_cat', $joinClause);
        if (is_null($workID)) {
            $items = $query->where(['note_man' => $uid])->whereNotEqual('item_status', '99')->findArray();
        } else {
            $items = $query->where(['note_man' => $uid, 'id' => $workID])->whereNotEqual('item_status',
                '99')->findArray();
        }
        foreach ($items as $key => $item) {
            $building = $items[$key]['building'];
            $category = $items[$key]['item_cat'];
            $state = $items[$key]['item_status'];

            $items[$key]['building'] = $this->getBuilding($building);
            $items[$key]['cat_name'] = $this->getCategory($category);
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
        $cat = ORM::forTable('repair_cat')->select('cat_name')->findOne($value);
        return $cat['cat_name'];
    }

    private function getItemStatus($value)
    {
        $building = ORM::forTable('repair_status')->select('status_name')->findOne($value);
        return $building['status_name'];
    }

    public function deleteUserItem($id, $uid)
    {
        if ($this->allowDelete($id, $uid)) {
            $this->deleteRepairItem($id);
            return true;
        } else {
            return false;
        }
    }

    private function allowDelete($id, $uid)
    {
        $status = ORM::forTable('repair_item')->select('item_status')->where(['note_man' => $uid])->findOne($id);
        if ($status && $status['item_status'] == 0) {
            return true;
        } else {
            return false;
        }

    }

    private function deleteRepairItem($id)
    {
        $item = ORM::forTable('repair_item')->findOne($id);
        $item->item_status = 99;
        $item->save();
    }

    public function readUserDetail($id, $uid)
    {
        $item = ORM::forTable('repair_item')->where(['id' => $id, 'note_man' => $uid])->findArray();
        return $item;
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
            $id = $items[$key]['id'];

            $items[$key]['building'] = $this->getBuilding($building);
            $items[$key]['item_cat'] = $this->getCategory($category);
            $items[$key]['item_status_name'] = $this->getItemStatus($state);
            $items[$key]['item_call'] = $this->getCall($id);
        }
        return $items;
    }

    public function getCall($itemID)
    {
        $count = ORM::forTable('repair_call')->where('repair_id', $itemID)->count();
        return $count;
    }

    public function readCall($Item)
    {
        $call = ORM::forTable('repair_call')->where('repair_id', $Item)->findArray();
        return $call;
    }

    public function readAllWork($start = null, $end = null)
    {
        if (is_null($start)) {
            $start = Carbon::parse("2016-01-01");
        }
        if (is_null($end)) {
            $end = Carbon::now();
        }
        $whereClause = 'note_time BETWEEN \'' . Carbon::parse($start) . '\' AND \'' . Carbon::parse($end) . '\'';
        $items = ORM::forTable('repair_item')->whereRaw($whereClause)->whereNotEqual('item_status', '99')->findArray();
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

    public function getAverageWorkDay($items)
    {
        $counter = 0;
        $spentTime = 0;
        foreach ($items as $key => $item) {
            if ($items[$key]['item_status'] == 3 || $items[$key]['item_status'] == 4) {
                $start = Carbon::parse($items[$key]['note_time']);
                $finish = Carbon::parse($items[$key]['ok_time']);
                $spentTime += $start->diffInDays($finish, true);
                $counter++;
            } else {
                continue;
            }
        }
        if ($counter == 0) {
            $counter = 1;
        }
        $average = round((float)$spentTime / (float)$counter, 2);
        return $average;
    }

}