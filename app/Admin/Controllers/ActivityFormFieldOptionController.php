<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BackToActivityList;
use App\Models\ActivityFormFieldOption;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityFormFieldOptionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        return Grid::make(new ActivityFormFieldOption(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('activity_form_id');
            $grid->column('name');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
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
        return Show::make($id, new ActivityFormFieldOption(), function (Show $show) {
            $show->field('id');
            $show->field('activity_form_id');
            $show->field('name');
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
        return Form::make(new ActivityFormFieldOption(), function (Form $form) {
            $form->display('id');
            $form->text('activity_form_id');
            $form->text('name');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
