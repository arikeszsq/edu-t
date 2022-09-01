<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\User;
use Carbon\Carbon;

class ActivityService
{
    public function getActivityById($id)
    {
        return Activity::query()->where('id', $id)->first();
    }

    public function type($id)
    {
        $activity = $this->getActivityById($id);
        return $activity->is_many;
    }

    public function lists()
    {
        return Activity::query()
            ->where('status', 1)
            ->where('end_time', '>', Carbon::now())
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param $id
     * @return array
     * @throws ObjectNotExistException
     */
    public function detail($id)
    {
        $activity = Activity::query()->where('id', $id)
            ->where('status', 1)
            ->where('end_time', '>', Carbon::now())
            ->first();
        if (!$activity) {
            throw new ObjectNotExistException('活动已结束，请核实');
        }
        $is_many = $activity->is_many;
        if ($is_many == Activity::is_many_单商家) {
            $data = $this->geSignalDetail($id);
        } else {
            $data = $this->getManyDetail($id);
        }
        return $data;
    }

    public function geSignalDetail($id)
    {
        $data = [];
        $activity = $this->getActivityById($id);
        $data['bg_banner'] = env('IMG_SERVE') . $activity->bg_banner;
        $data['title'] = $activity->title;
        $data['description'] = $activity->description;
        $data['ori_price'] = $activity->ori_price;
        $data['real_price'] = $activity->real_price;
        $data['end_time'] = $activity->end_time;
        $data['views_num'] = $activity->views_num;
        $data['buy_num'] = $activity->buy_num;
        $data['share_num'] = $activity->share_num;

        $groups = ActivitySignUser::getHasPayList($id);
        $users = [];
        foreach ($groups as $group) {
            $role = $group->role;
            $type = $group->type;
            if ($type == 2) {
                $msg = '单独购买';
            } else {
                if ($role == 1) {
                    $msg = '开团购买';
                } else {
                    $group_id = $group->group_id;
                    $activity_group = ActivityGroup::getGroupById($group_id);
                    $leader_id = $activity_group->leader_id;
                    $user = User::query()->find($leader_id);
                    $msg = '参加了' . $user->name . '的拼团';
                }
            }

            $users[] = [
                'avatar' => $group->user->avatar,
                'name' => $group->user->name,
                'msg' => $msg,
                'money' => $group->money,
            ];
        }
        $data['group'] = $users;
        return $data;

    }

    public function getManyDetail($id)
    {
        $data = [];
        return $data;
    }

}
