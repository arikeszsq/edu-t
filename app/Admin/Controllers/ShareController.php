<?php

namespace App\Admin\Controllers;

use App\Models\Share;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ShareController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Share::with(['activity', 'shareUser']), function (Grid $grid) {

            $grid->model()->orderBy('id','desc');
            $grid->column('id')->sortable();

            $grid->column('shareUser.name', '传播人头像');
            $grid->column('shareUser.name', '传播人昵称');
            $grid->column('activity.title', '活动名称');
            $grid->column('share_num');
            $grid->column('sign_num');
            $grid->column('pay_total_num', '付款总金额（元）');
            $grid->column('red_bag_num');
            $grid->column('red_bag_total_num');


            $grid->disableCreateButton();//禁用创建按钮
            $grid->disableActions();//禁用所有操作

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('shareUser.name', '传播人昵称')->width(4);
                $filter->like('activity.title', '活动名称')->width(4);
            });
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
        return Show::make($id, new Share(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('share_user_id');
            $show->field('share_num');
            $show->field('sign_num');
            $show->field('pay_total_num');
            $show->field('red_bag_num');
            $show->field('red_bag_total_num');
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
        return Form::make(new Share(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('share_user_id');
            $form->text('share_num');
            $form->text('sign_num');
            $form->text('pay_total_num');
            $form->text('red_bag_num');
            $form->text('red_bag_total_num');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
