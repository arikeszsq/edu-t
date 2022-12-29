<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ActivityComSign;
use App\Admin\Actions\Grid\ChangeStatus;
use App\Admin\Controllers\ActivityController;
use App\Models\Activity;
use App\Models\ActivitySignCom;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ManyController extends ActivityController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Activity(), function (Grid $grid) {
            // 设置表单提示值
            $grid->quickSearch('title')->placeholder('搜索活动标题');
            $grid->model()->where('is_many', 2);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('title','活动名称');
            $grid->column('start_time','开始时间');
            $grid->column('end_time','结束时间');
            $grid->column('stay_num','库存');
            $grid->column('views_num','浏览量');
            $grid->column('sign_success_num','成功报名');
            $grid->column('refund_num','退款订单量');
            $grid->column('refund_money','退款金额');
            $grid->column('total_pay_money','总付款金额');
            $grid->column('already_return_money','已返利总金额');
            $grid->column('earn_money','净利润');
            $grid->column('q_code','活动二维码');
            $grid->column('status','上下架状态')->using(Activity::$status_list)->label(['primary', 'warning']);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(6);
                $filter->like('title')->width(6);
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {

                $actions->append(new ActivityComSign('<span class="btn btn-sm btn-primary">报名</span>'));

                $status = $actions->row->status;
                if ($status == Activity::Status_已上架) {
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-primary">下架</span>'));
                } else {
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-warning">上架</span>'));
                }
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
            $form->text('title')->required();
            $form->image('bg_banner','背景图')->required()->autoUpload();
            $form->select('is_many')->options(Activity::$is_many_list)->required();
            $form->decimal('ori_price')->required();
            $form->decimal('real_price','开团价格')->required();
            $form->number('deal_group_num','成团人数')->required();
            $form->decimal('a_invite_money','A用户直接邀请奖励')->required();
            $form->decimal('a_other_money','A用户别人邀请获得的奖励')->required();
            $form->decimal('second_invite_money','非A二级邀请奖励')->required();
            $form->datetime('start_time')->required();
            $form->datetime('end_time')->required();
            $form->text('description');
            $form->editor('content');

            $form->image('share_bg','分享海报背景图')->autoUpload();

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
