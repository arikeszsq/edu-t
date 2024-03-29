<?php

namespace App\Http\Services;

use App\Exceptions\ObjectNotExistException;
use App\Http\Traits\AreaTrait;
use App\Http\Traits\ImageTrait;
use App\Models\Activity;
use App\Models\ActivityFormField;
use App\Models\ActivityGroup;
use App\Models\ActivityMusic;
use App\Models\ActivitySignCom;
use App\Models\ActivitySignUser;
use App\Models\Award;
use App\Models\Company;
use App\Models\CompanyChild;
use App\Models\UserActivityInvite;
use App\Models\UserViewCount;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    use ImageTrait;
    use AreaTrait;

    public function getActivityById($id)
    {
        return Activity::query()->where('id', $id)->first();
    }

    public function type($id)
    {
        $activity = $this->getActivityById($id);
        return [
            'type' => $activity->is_many,
            'has_over' => 1,
            'mini_over_bg' => $activity->mini_over_bg
        ];
    }

    public function lists()
    {
        $data = [];
        $lists = Activity::query()
            ->where('status', 1)
            ->where('end_time', '>', Carbon::now())
            ->orderBy('id', 'desc')
            ->get();
        foreach ($lists as $list) {
            $data[] = [
                'id' => $list->id,
                'title' => $list->title,
                'bg_banner' => $this->fullImgUrl($list->bg_banner),
                'is_many' => $list->is_many,
                'description' => $list->description,
                'content' => $list->content,
                'status' => $list->status,
                'start_time' => $list->start_time,
                'end_time' => $list->end_time,
            ];
        }
        return $data;
    }

    /**
     * @param $id
     * @param $user_id
     * @return array
     * @throws ObjectNotExistException
     */
    public function detail($id, $user_id)
    {
        $activity = Activity::query()->where('id', $id)
            ->where('status', 1)
            ->where('end_time', '>', Carbon::now())
            ->first();
        if (!$activity) {
            throw new ObjectNotExistException('活动不存在或已经下架，请核实', 10001);
        }

        $is_many = $activity->is_many;

        if ($is_many == Activity::is_many_单商家) {
            $data = $this->geSignalDetail($id);
        } else {
            $data = $this->getManyDetail($id, $user_id);
        }

        $music = ActivityMusic::query()->find($activity->music_id);
        $data['music_url'] = $music->file ?? '';
        $data['bg_banner'] = $activity->bg_banner;
        $data['title'] = $activity->title;
        $data['description'] = $activity->description;
        $data['ori_price'] = $activity->ori_price;
        $data['real_price'] = $activity->real_price;
        $data['start_time'] = $activity->start_time;
        $data['end_time'] = $activity->end_time;
        $data['views_num'] = UserViewCount::getActivityViewNum($id);
        $data['buy_num'] = ActivitySignUser::getHasPayNum($id);
        $data['share_num'] = UserActivityInvite::getActivityShareNum($id);
        $data['content'] = $activity->content;
        $data['vr_view'] = $activity->content;//虚拟浏览量
        $data['vr_share'] = $activity->content;//虚拟分享量
        $data['fields'] = $this->getFormFields($id);
        $data['course_num'] = $activity->course_num;//选择课程的数量
        $data['award_num'] = $activity->award_num;

        $data['group_num'] = ActivityGroup::query()->where('activity_id', $id)->count();
        $data['group_people_num'] = ActivitySignUser::query()->where('activity_id', $id)
            ->where('has_pay', 1)
            ->count();

        $data['group_people_list'] = ActivitySignUser::query()
            ->with('group.user')
            ->whereHas('group', function ($group) {
                $group->where('is_recommand', 1);
            })
            ->where('activity_id', $id)
            ->where('has_pay', 1)
            ->where('role', 1)//团长
            ->where('type', 1)//开团购买
            ->limit(8)
            ->get();

        $data['group_current'] = ActivitySignUser::query()
            ->with('group.user')
            ->where('has_pay', 1)
            ->where('activity_id', $id)
            ->where('role', 1)//团长
            ->where('type', 1)//开团购买
            ->orderBy('id', 'desc')
            ->first();

        return $data;
    }

    public function geSignalDetail($id)
    {
        $data = [];
        $sign_users = ActivitySignUser::getHasPayList($id);
        $pay_group_list = [];
        foreach ($sign_users as $group) {
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

            if ($group->user) {
                $pay_group_list[] = [
                    'avatar' => $group->user->avatar,
                    'name' => $group->user->name,
                    'msg' => $msg,
                    'money' => $group->money,
                    'pay_time' => $group->pay_time,
                ];
            }
        }
        $data['pay_group_list'] = $pay_group_list;
        return $data;

    }

    public function getCompanyMinGap($user_id_area, $company_id)
    {
        $childs = CompanyChild::query()->where('company_id', $company_id)->get();
        $gaps = [];
        foreach ($childs as $child) {
            $point = $child->map_points;
            $gaps[] = (float)$this->getDistance($point, $user_id_area);
        }
        return min($gaps);
    }

    public function getManyDetail($id, $user_id)
    {
        if ($user_id) {
            $user = \App\Models\User::query()->find($user_id);
        }
        $data = [];
        $activity = $this->getActivityById($id);
        $ori_price_array = explode('.', $activity->ori_price);
        if (isset($ori_price_array[0]) && $ori_price_array[0]) {
            $data['ori_price_before'] = $ori_price_array[0];
        } else {
            $data['ori_price_before'] = $activity->ori_price;
        }
        if (isset($ori_price_array[1]) && $ori_price_array[1]) {
            $data['ori_price_after'] = $ori_price_array[1];
        } else {
            $data['ori_price_after'] = 00;
        }

        $data['real_price'] = $activity->real_price;
        $real_price_array = explode('.', $activity->real_price);
        if (isset($real_price_array[0]) && $real_price_array[0]) {
            $data['real_price_before'] = $real_price_array[0];
        } else {
            $data['real_price_before'] = $activity->real_price;
        }
        if (isset($real_price_array[1]) && $real_price_array[1]) {
            $data['real_price_after'] = $real_price_array[1];
        } else {
            $data['real_price_after'] = 00;
        }

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
                'company_id' => $company->company_id,
                'company_name' => $company->company->name,
                'company_logo' => $this->fullImgUrl($company->company->logo),
                'school_num' => $school_num,
                'sign_user_num' => $sign_user_num,
                'course_num' => $course_num,
                'min_gap' => isset($user->map_points) && $user->map_points ? $this->getCompanyMinGap($user->map_points, $company->company_id) : 0
            ];
        }
        /** 活动的奖励 ***/
        $awards = Award::query()->where('status', Award::Status_有效)
            ->where('activity_id', $id)
            ->get();
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

        $sign_users = ActivitySignUser::getHasPayList($id);
        $pay_group_list = [];
        foreach ($sign_users as $group) {
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

            $pay_group_list[] = [
                'avatar' => $group->user->avatar,
                'name' => $group->user->name,
                'msg' => $msg,
                'money' => $group->money,
                'pay_time' => $group->pay_time,
            ];
        }

        $pay_num = count($pay_group_list);
        if ($pay_num == 1) {
            $pay_group_list[1] = $pay_group_list[0];
            $pay_group_list[2] = $pay_group_list[0];
            $pay_group_list[3] = $pay_group_list[0];
        } elseif ($pay_num == 2) {
            $pay_group_list[2] = $pay_group_list[0];
            $pay_group_list[3] = $pay_group_list[1];
        } elseif ($pay_num == 3) {
            $pay_group_list[3] = $pay_group_list[0];
        }
        $data['pay_group_list'] = $pay_group_list;

        return $data;
    }

    public function getFormFields($activity_id)
    {
        $fields = ActivityFormField::query()
            ->with('options')
            ->where('activity_id', $activity_id)
            ->get();
        $ret_field = [];
        foreach ($fields as $field) {
            $type = $field->type;
            if ($type == 1) {
                $ret_field[] = [
                    'field_name' => $field->field_name,
                    'field_en_name' => $field->field_en_name,
                    'type' => $field->type,
                ];
            } else {
                $options = [];
                foreach ($field->options as $option) {
                    $options[] = [
                        'option_id' => $option->id,
                        'option_title' => $option->name,
                    ];
                }
                $ret_field[] = [
                    'field_name' => $field->field_name,
                    'field_en_name' => $field->field_en_name,
                    'type' => $field->type,
                    'options' => $options,
                    'select_num'=>$field->select_num,
                ];
            }
        }
        return $ret_field;
    }


    /***
     * @param $inputs
     * @return int
     * @throws \Exception
     */
    public function inviteUser($inputs)
    {
        $activity_id = $inputs['activity_id'];
        $parent_user_id = $inputs['parent_user_id'];
        $invited_user_id = $inputs['invited_user_id'];
        $first = DB::table('user_activity_invite')
            ->where('activity_id', $activity_id)
            ->where('invited_user_id', $invited_user_id)
            ->first();
        if ($first) {
            return [
                'code' => 10001,
                'msg' => '本活动用户已被邀请,无需重复邀请'
            ];
        }
        $a_user_id = User::getAUidByUid($inputs['parent_user_id']);
        return UserActivityInvite::query()->insertGetId([
            'activity_id' => $activity_id,
            'A_user_id' => $a_user_id,
            'parent_user_id' => $parent_user_id,
            'invited_user_id' => $invited_user_id,
            'has_pay' => 2,//1已支付，2未支付
        ]);
    }

}
