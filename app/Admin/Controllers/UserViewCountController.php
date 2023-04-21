<?php

namespace App\Admin\Controllers;

use App\Models\UserViewCount;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserViewCountController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(UserViewCount::with(['activity', 'shareUser', 'user']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('user.avatar', '头像')->image('', '100%', '40');;
            $grid->column('user.nick_name', '用户昵称');
            $grid->column('user.mobile', '用户昵称');
            $grid->column('activity.title', '活动标题');
            $grid->column('has_sign')->display(function ($has_sign) {
                //付钱了就是已报名
                $array = [
                    1 => '已报名', 2 => '未报名'
                ];

                return $array[$has_sign]??'';
            });
            $grid->column('has_info', '预留信息')->display(function ($has_info) {
                $array = [
                    1 => '留下信息', 2 => '未留下信息'
                ];
                return $array[$has_info]??'';
            });
            $grid->column('view_num', '浏览次数');
            $grid->column('view_at', '访问时间');
            $grid->column('shareUser.name', '分享人');

            $grid->disableCreateButton();//禁用创建按钮
            $grid->disableActions();//禁用所有操作
            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('activity.title', '活动标题')->width(4);
                $filter->equal('has_sign')->select([
                    1 => '已报名', 2 => '未报名'
                ])->width(2);
                $filter->equal('has_info')->select([
                    1 => '留下信息', 2 => '未留下信息'
                ])->width(2);
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
        return Show::make($id, new UserViewCount(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('user_id');
            $show->field('has_info');
            $show->field('has_sign');
            $show->field('view_num');
            $show->field('view_at');
            $show->field('share_user_id');
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
        return Form::make(new UserViewCount(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('user_id');
            $form->text('has_info');
            $form->text('has_sign');
            $form->text('view_num');
            $form->text('view_at');
            $form->text('share_user_id');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
