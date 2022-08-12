<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BatchSign;
use App\Admin\Actions\Grid\RowSign;
use App\Models\Company;
use App\Models\CompanyCourse;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CompanyController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Company(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('short_name');
            $grid->column('intruduction');
            $grid->column('video_url');
            $grid->column('creater_id');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });

            $grid->batchActions([new BatchSign()]);

            $grid->actions([new RowSign()]);
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
        return Show::make($id, new Company(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('short_name');
            $show->field('intruduction');
            $show->field('video_url');
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
        return Form::make(Company::with(['children','courses']), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('short_name');
            $form->text('intruduction');
            $form->file('video_url');

            $form->hasMany('children', function (Form\NestedForm $form) {
                $form->width(2)->text('name');
                $form->width(2)->text('address','校区地址');
            })->useTable()->label('校区')->required();

            $form->hasMany('courses', function (Form\NestedForm $form) {
                $form->select('type')->options(CompanyCourse::Type_类型列表);
                $form->image('logo');
                $form->text('name');
                $form->decimal('price');
                $form->number('total_num');
            });


            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
