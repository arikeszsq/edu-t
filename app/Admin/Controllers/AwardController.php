<?php

namespace App\Admin\Controllers;

use App\Models\Award;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AwardController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Award(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('short_name');
            $grid->column('logo');
            $grid->column('description');
            $grid->column('invite_num');
            $grid->column('status');
            $grid->column('price');
            $grid->column('is_commander');
            $grid->column('group_ok');
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
        return Show::make($id, new Award(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('short_name');
            $show->field('logo');
            $show->field('description');
            $show->field('invite_num');
            $show->field('status');
            $show->field('price');
            $show->field('is_commander');
            $show->field('group_ok');
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
        return Form::make(new Award(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('short_name');
            $form->text('logo');
            $form->text('description');
            $form->text('invite_num');
            $form->text('status');
            $form->text('price');
            $form->text('is_commander');
            $form->text('group_ok');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
