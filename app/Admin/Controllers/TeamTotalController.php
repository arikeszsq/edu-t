<?php

namespace App\Admin\Controllers;

use App\Http\Traits\WeChatTrait;
use App\Models\Company;
use App\Models\CompanyChild;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class TeamTotalController extends AdminController
{

    public $title = '战队统计';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(CompanyChild::with(['company']), function (Grid $grid) {

            $grid->model()->orderBy('id', 'desc');

            $grid->column('id')->sortable();
            $grid->column('team_name', '战队名称');
            $grid->column('id1', '邀请人数')->display(function ($id) {
                return 111;
            });
            $grid->column('id2', '成单人数')->display(function ($id) {
                return 222;
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('team_name', '机构名称');
            });

            // 禁用详情按钮
            $grid->disableViewButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append('<a class="btn btn-sm btn-success" style="margin: 3px;;" href="/admin/company-child-team?activity_id=' . $actions->row->id . '">战队明细</a>');
            });
        });
    }
}
