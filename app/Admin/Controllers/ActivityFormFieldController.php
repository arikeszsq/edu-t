<?php

namespace App\Admin\Controllers;

use App\Models\ActivityFormField;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class ActivityFormFieldController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $activity_id = request()->get('activity_id');
        if ($activity_id) {
            Cache::put('activity_id', $activity_id);
        } else {
            $activity_id = Cache::get('activity_id');
        }
        if (!$activity_id) {
            return redirect('/admin/activity-one');
        }

        return Grid::make(new ActivityFormField(), function (Grid $grid) use ($activity_id) {
            $grid->model()->where('activity_id', $activity_id);
            $grid->column('id')->sortable();
            $grid->column('field_name');
            $grid->column('type')->display(function ($type) {
                $array = [
                    1 => '文本框', 2 => '单选框', 3 => '多选框'
                ];
                return $array[$type];
            });
            $grid->column('created_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });

            // 禁用详情按钮
            $grid->disableViewButton();
            // 禁用过滤器按钮
            $grid->disableFilterButton();

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
        return Show::make($id, new ActivityFormField(), function (Show $show) {
            $show->field('id');
            $show->field('activity_id');
            $show->field('field_name');
            $show->field('field_en_name');
            $show->field('type');
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
        $activity_id = request()->get('activity_id');
        if ($activity_id) {
            Cache::put('activity_id', $activity_id);
        } else {
            $activity_id = Cache::get('activity_id');
        }
        if (!$activity_id) {
            return redirect('/admin/activity-one');
        }

        return Form::make(new ActivityFormField(), function (Form $form) {
            $form->display('id');

            $form->text('field_name')->required();
            $form->select('type')->options([1 => '文本框', 2 => '单选框', 3 => '多选框'])->required();

            $form->hasMany('options', function (Form\NestedForm $form) {
                $form->text('name', '名称')->width(3);
            })->label('选项列表')->required();

            $form->display('created_at');
            $form->display('updated_at');

            $form->hidden('activity_id');
            $form->hidden('field_en_name');

            $form->saving(function (Form $form) {
                $activity_id = Cache::get('activity_id');
                $form->activity_id = (int)$activity_id;
                $form->field_en_name = 'af_' . date('mdHis');
            });
        });
    }
}
