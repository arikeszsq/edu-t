<?php

namespace App\Admin\Controllers;

use App\Models\ActivitySignCompany;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Controllers\AdminController;

class ActivitySignCompanyController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ActivitySignCompany(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('activity_id');
            $grid->column('company_id');
            $grid->column('creater_id');
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
        return Show::make($id, new ActivitySignCompany(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('company_id');
            $show->field('creater_id');
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
        return Form::make(new ActivitySignCompany(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('company_id');
            $form->text('creater_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
