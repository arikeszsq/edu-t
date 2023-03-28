<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BackToActivityList;
use App\Models\Activity;
use App\Models\CompanyChildTeam;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class CompanyChildTeamController extends AdminController
{
    public $title = '战队详情';

    /***
     *
     *
     *
     *
     * @return Grid
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function grid()
    {
        $activity_id = request()->get('activity_id');
        $team_id = request()->get('team_id');
        if ($activity_id) {
            Cache::put('activity_id', $activity_id);
            Cache::put('team_id', $team_id);
        } else {
            $activity_id = Cache::get('activity_id');
        }
        return Grid::make(CompanyChildTeam::with(['teacher', 'companychild']), function (Grid $grid) use ($team_id) {
            $grid->model()->where('child_id', $team_id)->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('activity', '活动名称')->display(function () {
                $activity_id = Cache::get('activity_id');
                $activity = Activity::getActivityById($activity_id);
                return $activity->title ?? '';
            });
            $grid->column('companychild.team_name', '战队名称');
            $grid->column('teacher.name', '老师名字');

            $grid->column('invite_num', '邀请人数')->display(function () {
                $user_id = $this->user_id;
                $activity_id = Cache::get('activity_id');
                return CompanyChildTeam::getUserInviteNum($activity_id, $user_id);
            });

            $grid->column('success_num', '成单人数')->display(function () {
                $user_id = $this->user_id;
                $activity_id = Cache::get('activity_id');
                return CompanyChildTeam::getUserSuccessNum($activity_id, $user_id);
            });

            $grid->disableActions();//禁用所有操作
            $grid->disableCreateButton();//禁用创建按钮
            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('teacher.name', '老师名字')->width(4);
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
            });
        });
    }

}
