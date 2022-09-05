<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use Carbon\Carbon;

class GroupService
{

    public function lists($inputs)
    {
        $list = [];
        $activity_id = $inputs['activity_id'];
        $query = ActivityGroup::query()
            ->with('user')
            ->where('activity_id', $activity_id)
            ->where('status', ActivityGroup::Status_有效_已支付);

        if (isset($inputs['leader_id']) && $inputs['leader_id']) {
            $query->where('leader_id', $inputs['leader_id']);
        }

        if (isset($inputs['leader_wx_name']) && $inputs['leader_wx_name']) {
            $query->where('leader_wx_name', 'like', '%' . $inputs['leader_wx_name'] . '%');
        }

        if (isset($inputs['leader_boy_name']) && $inputs['leader_boy_name']) {
            $query->where('leader_boy_name', 'like', '%' . $inputs['leader_boy_name'] . '%');
        }

        $activity_groups = $query->orderBy('current_num', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activity_groups as $group) {
            $list[] = [
                'avatar' => $group->user->name,
                'leader_name' => $group->user->name,
                'leader_id' => $group->leader_id,
                'num' => $group->num,
                'current_num' => $group->current_num,
                'finished' => $group->finished
            ];
        }
        return $list;
    }

    public function userList($id)
    {
        $lists = ActivitySignUser::query()
            ->with('user')
            ->where('group_id', $id)
            ->where('status', ActivitySignUser::Status_已支付)
            ->orderBy('role', 'asc')
            ->orderBy('pay_time', 'desc')
            ->get();

        $users = [];
        foreach ($lists as $list) {
            $users[] = [
                'role' => $list->role,//1团长  2团员
                'avatar' => $list->user->avatar,
                'name' => $list->user->name,
                'created_at' => date('Y-m-d H:i', strtotime($list->created_at))
            ];
        }
        return $users;
    }

}