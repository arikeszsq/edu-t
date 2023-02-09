<?php

namespace App\Admin\Controllers;

use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\ActivitySignUserCourse;
use App\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class OrderController extends ActivitySignUserController
{

    public $title = '订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(ActivitySignUser::with(['activity', 'user']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('order_no', '订单编号');
            $grid->column('user.avatar', '用户头像')->image('', 50, 50);
            $grid->column('user.nick_name', '用户昵称');
            $grid->column('sign_name', '报名信息')->display(function ($sign_name) {
                $sex = '';
                if (isset($this->sign_sex) && $this->sign_sex) {
                    $sex .= ActivitySignUser::Sex_List[$this->sign_sex];
                }
                return $sign_name . '-' . $this->sign_mobile . '-' . $sex . $this->sign_age . '岁';
            });

            $grid->column('sign_name', '姓名');
            $grid->column('sign_mobile', '电话');
            $grid->column('role', '团内身份')->display(function ($role) {
                $array = [
                    1 => '团长',
                    2 => '团员'
                ];
                return $array[$role];
            });
            $grid->column('group_id', '团内人数')->display(function ($group_id) {

                $group = ActivityGroup::getGroupById($group_id);
                if ($group) {
                    return $group->current_num;
                } else {
                    return 0;
                }
            });
            $grid->column('activity.title', '活动名称');
            $grid->column('activity.title', '商品名称');
            $grid->column('money', '支付金额');
            $grid->column('status', '订单状态')->display(function ($status) {
                return ActivitySignUser::Status_支付[$status];
            })->label([1 => 'danger', 2 => 'warning', 3 => 'success']);
            $grid->column('created_at', '下单时间');
            $grid->column('refund_status', '退款状态');
            $grid->column('refund_time', '退款时间');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('activity.title', '活动名称')->width(2);
                $filter->like('order_no', '订单号')->width(2);
                $filter->like('sign_name', '姓名')->width(2);
                $filter->like('sign_mobile', '电话')->width(2);
                $filter->like('status', '状态')->select(ActivitySignUser::Status_支付)->width(2);
            });

            $grid->disableCreateButton();//禁用创建按钮
            $grid->disableEditButton();//禁用编辑
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, ActivitySignUser::with(['activity', 'group', 'user']), function (Show $show) use ($id) {

            $order = ActivitySignUser::query()->find($id);
            $is_many = Activity::isManyById($order->activity_id);

            $show->field('id');
            $show->field('activity.title', '活动名称');
            $show->field('group.name', '团名');
            $show->field('role');
            $show->role('身份')->using([1 => '团长', 2 => '团员']);
            $show->field('user.name', '姓名');
            $show->divider();
            $show->type('购买方式')->using([1 => '团购', 2 => '单独购买']);
            $show->field('order_no', '订单号');
            $show->field('money', '订单金额');
            $show->has_pay('付款状态')->using([1 => '待支付', 2 => '支付取消', 3 => '支付成功']);
            $show->field('pay_time', '付款时间');
            $show->divider();
            $show->field('sign_name', '报名人');
            $show->field('sign_mobile', '报名手机号');
            $show->divider();
            if ($is_many == Activity::is_many_多商家) {
                $show->field('sign_age', '报名人年龄');
                $show->sign_sex('报名人性别')->using([1 => '男', 2 => '女']);
            }
            if ($is_many == Activity::is_many_单商家) {
                $show->field('info_one', '信息')->as(function ($info_one) {
                    $lists = json_decode($info_one, true);
                    $html = '';
                    foreach ($lists as $key => $value) {
                        if (is_array($value)) {
                            $str = join('、', $value);
                            $html .= $key . '：' . $str . '； ';
                        } else {
                            $html .= $key . '：' . $value . '； ';
                        }
                    }
                    return $html;
                });
            }
            if ($is_many == Activity::is_many_多商家) {
                $show->relation('courses', '课程信息', function ($model) {
                    $grid = new Grid(ActivitySignUserCourse::with(['school', 'course']));
                    $grid->model()->where('order_num', $model->order_no);
//                $grid->number();
//                $grid->id();
                    $grid->column('school.name', '学校名称');
                    $grid->column('course.name', '课程名称');
                    $grid->filter(function ($filter) {
//                    $filter->like('')->width('300px');
                    });
                    $grid->disableCreateButton();//禁用创建按钮
                    $grid->disableActions();//禁用所有操作
                    return $grid;
                });
            }

//            $show->field('is_agree');
            $show->field('created_at');
            $show->field('updated_at');

            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
//                    $tools->disableList();
                    $tools->disableDelete();
                    // 显示快捷编辑按钮
//                    $tools->showQuickEdit();
                });

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ActivitySignUser(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('group_id');
            $form->text('role');
            $form->text('user_id');
            $form->text('type');
            $form->text('creater_id');
            $form->text('share_q_code');
            $form->text('order_no');
            $form->text('money');
            $form->text('has_pay');
            $form->text('status');
            $form->text('pay_time');
            $form->text('pay_cancel_time');
            $form->text('sign_name');
            $form->text('sign_mobile');
            $form->text('sign_age');
            $form->text('sign_sex');
            $form->text('is_agree');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
