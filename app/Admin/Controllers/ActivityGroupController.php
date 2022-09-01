<?php

namespace App\Admin\Controllers;

use App\Models\ActivityGroup;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityGroupController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ActivityGroup(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('activity_id');
            $grid->column('name');
            $grid->column('leader_id');
            $grid->column('creater_id');
            $grid->column('success_time');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
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
        return Show::make($id, new ActivityGroup(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('name');
            $show->field('leader_id');
            $show->field('creater_id');
            $show->field('success_time');
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
        return Form::make(new ActivityGroup(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('name');
            $form->text('leader_id');
            $form->text('creater_id');
            $form->text('success_time');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
