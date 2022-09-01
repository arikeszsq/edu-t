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

        $activity_groups = $query->orderBy('current_num', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activity_groups as $group) {
            $list[] = [
                'avatar' => $group->user->name,
                'leader_name' => $group->user->name,
                'leader_id' => $group->leader_id,
                'num' => $group->num,
                'current_num' => $group->current_num
            ];
        }
        return $activity_groups;
    }

}
