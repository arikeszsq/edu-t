<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Export\GridTool;
use App\Admin\Actions\Grid\BackToActivityList;
use App\Admin\Actions\Grid\Manage\BeGrouper;
use App\Admin\Actions\Grid\Manage\NewGroupDeal;
use App\Admin\Actions\Grid\Manage\RowRefund;
use App\Admin\Actions\Grid\MoveGroup;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\UserActivityInvite;
use App\User;
use Dcat\Admin\Grid;
use Illuminate\Support\Facades\Cache;

class UserSignManageController extends ActivitySignUserController
{

    public $title = '报名管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $activity_id = request()->get('activity_id');
        if ($activity_id) {
            Cache::put('activity_id', $activity_id);
        } else {
            $activity_id = Cache::get('activity_id');
        }
        if (!$activity_id) {
            return redirect('/admin/activity-one');
        }
        return Grid::make(ActivitySignUser::with(['activity', 'user', 'group', 'grouper', 'inviter']), function (Grid $grid) use ($activity_id) {
            $grid->showColumnSelector();
            $grid->export()->rows(function ($rows) {
                foreach ($rows as $index => &$row) {
                    $array = [
                        1 => '团长',
                        2 => '团员'
                    ];
                    $role = $row['role'];

                    $array_is_true = [
                        1 => '真实数据',
                        2 => '假数据'
                    ];
                    $is_true = $row['is_true'];

//                    $row->{"activity.title"} = $row->activity->title;
                    $row['role'] = $array[$role];
                    $row['is_true'] = $array_is_true[$is_true];

                    $activity_id = $row['activity_id'];
                    $user_id = $row['user_id'];
                    $money = 0;
                    $activity = Activity::getActivityById($activity_id);
                    $user_invite = UserActivityInvite::query()->where('activity_id', $activity_id)
                        ->where('invited_user_id', $user_id)
                        ->where('has_pay', 1)
                        ->orderBy('id', 'desc')->first();
                    if ($user_invite) {
                        if ($user_invite->A_user_id == $user_invite->parent_user_id) {
                            //A用户直接邀请
                            $money += $activity->a_invite_money;
                        } else {
                            $money += $activity->a_other_money;
                            $money += $activity->second_invite_money;
                        }
                        $row['a_money'] = $money;
                    }

                }
                return $rows;
            });

            $grid->model()->where('activity_id', $activity_id);
            $grid->column('id')->sortable();
            $grid->model()->orderBy('id', 'desc');
            $grid->column('activity.title', '活动名称');

            $grid->column('avatar', '用户头像')->display(function ($user_id) {
                $user = User::query()->find($user_id);
                if (isset($user->avatar) && $user->avatar) {
                    return '<img src="' . $user->avatar . '" style="width: 60px;height: 60px;">';
                } else {
                    return '';
                }
            });
            $grid->column('user.nick_name', '用户昵称');

            $grid->column('user.name', '报名名字');
            $grid->column('user.mobile', '报名电话');
            $grid->column('role', '身份')->display(function ($role) {
                $array = [
                    1 => '团长',
                    2 => '团员'
                ];
                return $array[$role];
            });
            $grid->column('is_true', '身份属性')->display(function ($is_true) {
                $array = [
                    1 => '真实数据',
                    2 => '假数据'
                ];
                return $array[$is_true];
            });
            $grid->column('grouper.nick_name', '团长');
            $grid->column('group_id', '团人数')->display(function ($group_id) {
                $group = ActivityGroup::query()->find($group_id);
                return $group->current_num ?? 0;
            });
            $grid->column('inviter.avatar', '推荐人头像')->display(function () {
                $invite_id = $this->invite_id;
                $user = User::query()->find($invite_id);
                if ($user) {
                    return '<img src="' . $user->avatar . '" style="width: 60px;height: 60px;">';
                } else {
                    return '';
                }
            });
            $grid->column('inviter.nick_name', '推荐人昵称');
            $grid->column('inviter.name', '推荐人姓名');
            $grid->column('inviter.mobile', '推荐人手机号');

            $grid->column('info', '报名信息')->display(function ($info) {
                $lists = json_decode($info, true);
                $html = '';
                foreach ($lists as $key => $value) {
                    if (is_array($value)) {
                        $str = join('、', $value);
                        $html .= '<span style="color: blue;">' . $key . '</span>：' . $str . '； <br>';
                    } else {
                        $html .= '<span style="color: blue;">' . $key . '</span>：' . $value . '； <br>';
                    }
                }
                return $html;
            });
            $grid->column('money', '付款金额');
            $grid->column('a_money', '返利金额')->display(function () {
                $money = 0;
                $activity_id = $this->activity_id;
                $activity = Activity::getActivityById($activity_id);
                $user_invite = UserActivityInvite::query()->where('activity_id', $this->activity_id)
                    ->where('invited_user_id', $this->user_id)
                    ->where('has_pay', 1)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($user_invite) {
                    if ($user_invite->A_user_id == $user_invite->parent_user_id) {
                        //A用户直接邀请
                        $money += $activity->a_invite_money;
                    } else {
                        $money += $activity->a_other_money;
                        $money += $activity->second_invite_money;
                    }
                    return $money;
                }

                return $money;

            });//这个订单给老师的钱
            $grid->column('pay_time', '支付订单时间');
            $grid->column('refund_status', '退款状态')->display(function () {
                if ((int)$this->refund_status == 1) {
                    return '已退款';
                }
            });
            $grid->column('refund_time', '退款时间');

            // 禁用详情按钮
            $grid->disableViewButton();
            // 禁用过滤器按钮
            $grid->disableFilterButton();
            $grid->filter(function (Grid\Filter $filter) {
                // 展开过滤器
                $filter->expand();
                $filter->like('user.nick_name', '用户昵称', '用户昵称')->width(3);
                $filter->equal('role', '选择身份')->select([1 => '团长', 2 => '团员'])->width(3);
                $filter->like('user.name', '报名名字')->width(3);
                $filter->like('user.mobile', '报名电话')->width(3);
                $filter->like('grouper.nick_name', '团长名字')->width(3);
                $filter->equal('is_true', '身份属性')->select([
                    1 => '真实数据',
                    2 => '假数据'
                ])->width(3);
                $filter->like('inviter.nick_name', '推荐人昵称')->width(3);
                $filter->like('info', '报名信息')->width(3);

            });

            $grid->disableCreateButton();//禁用创建按钮
            $grid->disableEditButton();//禁用编辑
            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new MoveGroup());
                $actions->append(new NewGroupDeal());
                $actions->append(new BeGrouper());
                $actions->append(new RowRefund());
            });
        });
    }

}
