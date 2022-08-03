<?php

namespace App\Admin\Controllers;

use App\Models\UserAward;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserAwardController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new UserAward(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('activity_id');
            $grid->column('user_id');
            $grid->column('award_id');
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
        return Show::make($id, new UserAward(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('user_id');
            $show->field('award_id');
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
        return Form::make(new UserAward(), function (Form $form) {
            $form->display('id');
            $form->text('activity_id');
            $form->text('user_id');
            $form->text('award_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
