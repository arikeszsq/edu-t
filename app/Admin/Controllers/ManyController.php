<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ActivityComSign;
use App\Admin\Actions\Grid\ChangeStatus;
use App\Admin\Controllers\ActivityController;
use App\Models\Activity;
use App\Models\ActivityMusic;
use App\Models\ActivitySignCom;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ManyController extends ActivityController
{
    public $title = '多商家活动';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Activity(), function (Grid $grid) {
            $grid->model()->where('is_many', Activity::is_many_多商家);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('title', '活动名称');
            $grid->column('start_time', '活动开始时间')->display(function ($start_time) {
                return date('Y-m-d', strtotime($start_time));
            });
            $grid->column('end_time', '活动结束时间')->display(function ($end_time) {
                return date('Y-m-d', strtotime($end_time));
            });
            $grid->column('stay_num', '库存');
            $grid->column('views_num', '浏览量');
            $grid->column('sign_success_num', '成功报名');
            $grid->column('refund_num', '退款订单量');
            $grid->column('refund_money', '退款金额');
            $grid->column('total_pay_money', '总付款金额');
            $grid->column('already_return_money', '已返利总金额');
            $grid->column('earn_money', '净利润');
            $grid->column('q_code', '活动二维码');
            $grid->column('status', '上下架状态')->using(Activity::$status_list)->label(['primary', 'warning']);

            // 禁用过滤器按钮
            $grid->disableFilterButton();
            $grid->filter(function (Grid\Filter $filter) {
                // 展开过滤器
                $filter->expand();
                $filter->like('title', '搜索活动标题')->width(4);
                $filter->equal('status', '上下架状态')->select(Activity::$status_list)->width(2);
            });


            // 禁用详情按钮
            $grid->disableViewButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {

                $status = $actions->row->status;
                if ($status == Activity::Status_已上架) {
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-primary">下架</span>'));
                } else {
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-warning">上架</span>'));
                }
                $actions->append(new ActivityComSign('<span  style="margin: 3px;" class="btn btn-sm btn-primary">报名</span>'));

                $actions->append('<a class="btn btn-sm btn-danger" style="margin: 3px;" href="/admin/user-sign-manage?activity_id=' . $actions->row->id . '">报名管理</a>');

            });
        });
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Activity(), function (Show $show) {
            $show->field('id');
            $show->field('title');
            $show->field('is_many');
            $show->field('description');
            $show->field('content');
            $show->field('ori_price');
            $show->field('real_price');
            $show->field('status');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {


        return Form::make(new Activity(), function (Form $form) {
            $form->display('id');
            $form->multipleImage('bg_banner', '轮播图')->required()->saveFullUrl()->autoUpload()->saving(function ($v) {
                return json_encode($v);
            });
            $form->text('title', '活动名称')->required();
            $form->text('description', '活动描述')->required();
            $form->datetime('start_time', '活动开始时间')->required();
            $form->datetime('end_time', '活动结束时间')->required();
            $form->decimal('ori_price', '原价')->required();
            $form->decimal('real_price', '开团价')->required();
            $form->number('deal_group_num', '成团人数')->required();
            $form->number('course_num', '选择课程数量')->default(4)->required();
            //music_id 活动音乐
            $form->select('music_id', '活动音乐')->options(ActivityMusic::getMusicArray());
            $form->decimal('a_invite_money', 'A用户直接邀请奖励')->required();
            $form->decimal('a_other_money', 'A用户别人邀请获得的奖励')->required();
            $form->decimal('second_invite_money', '非A二级邀请奖励')->required();
            $form->number('vr_view', '虚拟浏览量')->required();
            $form->number('vr_share', '虚拟分享量')->required();
            $form->image('share_bg', '分享海报背景图')->autoUpload();
            $form->image('mini_bg', '小程序封面')->saveFullUrl()->autoUpload();
            $form->image('mini_over_bg', '小程序结束图')->saveFullUrl()->autoUpload();
            $form->multipleImage('content', '活动的详情图片')->sortable()->saveFullUrl()->autoUpload()->saving(function ($v) {
                return json_encode($v);
            });
            $form->display('created_at');
            $form->display('updated_at');

            $form->hidden('is_many');
            $form->saving(function (Form $form) {
                $form->is_many = Activity::is_many_多商家;
            });

            $form->tools(function (Form\Tools $tools) {
                // 去掉跳转详情页按钮
                $tools->disableView();
            });
            $form->footer(function ($footer) {
                // 去掉`重置`按钮
                $footer->disableReset();
                // 去掉`查看`checkbox
                $footer->disableViewCheck();
            });
        });
    }
}
