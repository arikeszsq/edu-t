<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BackToActivityList;
use App\Http\Traits\WeChatTrait;
use App\Models\Activity;
use App\Models\Company;
use App\Models\CompanyChild;
use App\Models\CompanyChildTeam;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class TeamTotalController extends AdminController
{

    public $title = '战队统计';

    /**
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
        if ($activity_id) {
            Cache::put('activity_id', $activity_id);
        } else {
            $activity_id = Cache::get('activity_id');
        }

        return Grid::make(CompanyChild::with(['company']), function (Grid $grid) use ($activity_id) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();

            $grid->column('activity', '活动名称')->display(function () {
                $activity_id = Cache::get('activity_id');
                $activity = Activity::getActivityById($activity_id);
                return $activity->title ?? '';
            });

            $grid->column('team_name', '战队名称');

            $grid->column('invite_num', '邀请人数')->display(function () {
                $team_id = $this->id;
                $activity_id = Cache::get('activity_id');
                return CompanyChildTeam::getTeamInviteNum($activity_id, $team_id);
            });
            $grid->column('success_num', '成单人数')->display(function () {
                $team_id = $this->id;
                $activity_id = Cache::get('activity_id');
                return CompanyChildTeam::getTeamSuccessNum($activity_id, $team_id);
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->equal('id')->width(2);
                $filter->like('team_name', '战队名称')->width(4);
            });

            // 禁用按钮
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableCreateButton();//禁用创建按钮

            $grid->actions(function (Grid\Displayers\Actions $actions) use ($activity_id) {
                $actions->append('<a class="btn btn-sm btn-success" style="margin: 3px;;" href="/admin/company-child-team?team_id=' . $actions->row->id . '&activity_id=' . $activity_id . '">战队明细</a>');
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->append(new BackToActivityList());
            });
        });
    }
}
