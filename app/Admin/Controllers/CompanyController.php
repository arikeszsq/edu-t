<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BackToActivityList;
use App\Admin\Actions\Grid\BatchSign;
use App\Admin\Actions\Grid\BatchSignNew;
use App\Admin\Actions\Grid\RowSign;
use App\Models\Company;
use App\Models\CompanyCourse;
use App\Models\CompanyCourseType;
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
        return Grid::make(new Company(), function (Grid $grid){

            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('short_name');
            $grid->column('intruduction');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });

            $grid->batchActions([
                new BatchSignNew('报名', 1),
                new BatchSignNew('取消报名', 0)
            ]);


            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append('<a class="btn btn-sm btn-warning" style="margin: 3px;"
                href="/admin/company-child/create?activity_id='.$actions->row->id.'">添加校区</a>');
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
        return Form::make(Company::with(['children', 'courses']), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('short_name');
            $form->text('intruduction');

            $form->image('logo','公司logo')
                ->move('logo_images')
                ->uniqueName()
                ->autoUpload()
                ->width(6);

            $form->file('video_url')->autoUpload()->width(6);

            $form->hasMany('courses', function (Form\NestedForm $form) {
                $form->select('type')->options(CompanyCourseType::getListArray())->width(3);

                $form->image('logo_c','课程logo')
                    ->uniqueName()
                    ->autoSave()
                    ->autoUpload()
                    ->width(3);

                $form->text('name')->width(3);
                $form->decimal('price', '价格')->width(3);
                $form->number('total_num', '名额数')->width(3);
            })->label('课程')->required();


            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
