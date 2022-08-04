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
            $grid->column('id')->sortable();
            $grid->column('title');
            $grid->column('is_many');
            $grid->column('description');
//            $grid->column('content');
//            $grid->column('ori_price');
//            $grid->column('real_price');
            $grid->column('status');
            $grid->column('start_time');
            $grid->column('end_time');
            $grid->column('created_at');
//            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });


//            $grid->actions(new ChangeStatus());
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $status = $actions->row->status;
                if($status == Activity::Status_已上架){
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-success">下架</span>'));
                }else{
                    $actions->append(new ChangeStatus('<span class="btn btn-sm btn-primary">上架</span>'));
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
            $form->text('title');
            $form->text('is_many');
            $form->text('description');
            $form->text('content');
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
