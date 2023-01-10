<?php

namespace App\Admin\Controllers;

use App\Models\Complaint;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ComplaintController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Complaint::with(['activity', 'user']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user.nick_name', '用户昵称');
            $grid->column('user.avatar', '用户头像');
            $grid->column('user.name', '投诉者姓名');
            $grid->column('user.mobile', '投诉者电话');
            $grid->column('activity.title', '活动来源');
            $grid->column('type');
            $grid->column('content');
            $grid->column('created_at','投诉时间')->sortable();

            $grid->disableActions();//禁用行所有操作
            $grid->disableViewButton();// 禁用详情按钮
            $grid->disableCreateButton();//禁用创建按钮
            $grid->disableFilterButton();// 禁用过滤器按钮

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->equal('user.nick_name', '用户昵称')->width(3);
                $filter->like('user.name', '投诉者姓名')->width(3);;
                $filter->like('user.mobile', '投诉者电话')->width(3);;
                $filter->like('activity.title', '活动来源')->width(3);;
                $filter->like('type')->width(3);;
                $filter->like('content')->width(3);;

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
        return Show::make($id, new Complaint(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('activity_id');
            $show->field('type');
            $show->field('content');
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
        return Form::make(new Complaint(), function (Form $form) {
            $form->display('id');
            $form->textarea('content');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
