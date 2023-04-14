<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BackToActivityList;
use App\Models\Activity;
use App\Models\Award;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class AwardController extends AdminController
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
            return redirect('/admin/activity-many');
        }
        return Grid::make(Award::with(['activity']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('activity.title', '活动名称');
            $grid->column('name');
            $grid->column('logo')->image(env('IMG_SERVE'), '100%', '40');
            $grid->column('invite_num');
            $grid->column('status')->select(Award::Status_list);
            $grid->column('is_commander')->select(Award::Yes_1_No_2_list);
            $grid->column('group_ok')->select(Award::Yes_1_No_2_list);

            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
            });

            $grid->quickSearch('name')->placeholder('搜索标题');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(6);
                $filter->like('activity.title', '活动名称')->width(6);
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
            $show->field('logo')->image();
            $show->field('description');
            $show->field('invite_num');
            $show->field('status')->using(Award::Status_list);
            $show->field('price');
            $show->field('is_commander')->using(Award::Yes_1_No_2_list);
            $show->field('group_ok')->using(Award::Yes_1_No_2_list);
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
            return redirect('/admin/company');
        }
        return Form::make(new Award(), function (Form $form) use ($activity_id) {
            $form->display('id');
            $acs = Activity::getActivityListOptions();
            $form->select('activity_id', '活动名称')
                ->default($activity_id)
                ->options($acs)
                ->required();
            $form->text('name');
            $form->text('short_name');
            $form->image('logo')->autoUpload();
            $form->text('description');
            $form->number('invite_num');
            $form->decimal('price');
            $form->select('status')->options(Award::Status_list);
            $form->select('is_commander')->options(Award::Yes_1_No_2_list);
            $form->select('group_ok')->options(Award::Yes_1_No_2_list);
        });
    }
}
