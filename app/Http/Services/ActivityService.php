<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Http\Traits\ImageTrait;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignCom;
use App\Models\ActivitySignUser;
use App\Models\Award;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    use ImageTrait;

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
        $data['bg_banner'] = $this->fullImgUrl($activity->bg_banner);
        $data['title'] = $activity->title;
        $data['description'] = $activity->description;
        $data['ori_price'] = $activity->ori_price;
        $data['real_price'] = $activity->real_price;
        $data['end_time'] = $activity->end_time;
        $data['views_num'] = $activity->views_num;
        $data['buy_num'] = $activity->buy_num;
        $data['share_num'] = $activity->share_num;
        $data['content'] = $activity->content;

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
                'pay_time' => $group->pay_time,
            ];
        }
        $data['group'] = $users;
        return $data;

    }

    public function getManyDetail($id)
    {
        $data = [];
        $activity = $this->getActivityById($id);
        $data['bg_banner'] = $this->fullImgUrl($activity->bg_banner);
        $data['title'] = $activity->title;
        $data['description'] = $activity->description;
        $data['ori_price'] = $activity->ori_price;
        $data['real_price'] = $activity->real_price;
        $data['end_time'] = $activity->end_time;
        $data['views_num'] = $activity->views_num;
        $data['buy_num'] = $activity->buy_num;
        $data['share_num'] = $activity->share_num;
        $data['content'] = $activity->content;
        $companies = ActivitySignCom::query()->with('company')
            ->where('activity_id', $id)
            ->get();
        $data_video = [];
        $data_company = [];
        foreach ($companies as $company) {
            $data_video[] = [
                'company_name' => $company->company->name,
                'company_logo' => $this->fullImgUrl($company->company->logo),
                'video_url' => $this->fullImgUrl($company->company->video_url)
            ];

            $school_num = DB::table('company_child')
                ->where('company_id', $company->company_id)
                ->count();

            $sign_user_num = DB::table('activity_sign_user_course')
                ->where('company_id', $company->company_id)
                ->count();

            $course_num = DB::table('company_course')
                ->where('company_id', $company->company_id)
                ->count();

            $data_company[] = [
                'company_name' => $company->company->name,
                'company_logo' => $this->fullImgUrl($company->company->logo),
                'school_num' => $school_num,
                'sign_user_num' => $sign_user_num,
                'course_num' => $course_num,
            ];
        }
        $awards = Award::query()->where('status', Award::Status_有效)->get();
        $data_award = [];
        foreach ($awards as $award) {
            $data_award[] = [
                'name' => $award->name,
                'short_name' => $award->short_name,
                'logo' => $this->fullImgUrl($award->logo),
                'description' => $award->description,
                'price' => $award->price,
            ];
        }

        $data['video'] = $data_video;
        $data['company_list'] = $data_company;
        $data['award'] = $data_award;

        return $data;
    }

}
