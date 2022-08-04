<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ChangeStatus;
use App\Models\Activity;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityController extends AdminController
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



            $grid->model()->orderBy('id', 'desc');



            $grid->column('title')->qrcode(function () {
                return 'http://www.baidu.com';
            }, 200, 200);


            $grid->column('id')->sortable();
//            $grid->column('title');
//            $grid->column('is_many')->select(Activity::$is_many_list);
            $grid->column('is_many')->using(Activity::$is_many_list)->label([1 => 'danger', 2 => 'success']);
//            $grid->column('description');
//            $grid->column('content');
//            $grid->column('ori_price');
//            $grid->column('real_price');
//            $grid->column('status')->select(Activity::$status_list);

//            $grid->column('status')->display(function($status){
//                return Activity::$status_list[$status];
//            })->label(['primary','warning']);
            $grid->column('status')->using(Activity::$status_list)->label(['primary', 'warning']);
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(6);
                $filter->like('title')->width(6);
            });

//            $grid->enableDialogCreate();
//            $grid->actions(new ChangeStatus());
            $grid->actions(function (Grid\Displayers\Actions $actions) {
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
     *
     * @param mixed $id
     *
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
            $form->select('is_many')->options(Activity::$is_many_list)->required()->width(3);
            $form->text('description');
            $form->textarea('content');
            $form->text('ori_price');
            $form->text('real_price');
            $form->text('status');
            $form->text('start_time');
            $form->text('end_time');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
