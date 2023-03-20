<?php

namespace App\Admin\Controllers;

use App\Models\Company;
use App\Models\CompanyChild;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;

class CompanyChildController extends AdminController
{
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
            $grid->column('company.name', '企业名称');
            $grid->column('name');
            $grid->column('map_area');
            $grid->column('mobile', '联系电话');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('company.name', '企业名称');
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
        return Show::make($id, CompanyChild::with(['company']), function (Show $show) {
            $show->field('id');
            $show->field('company.name', '企业名称');
            $show->field('name');
            $show->field('map_area');
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
        return Form::make(new CompanyChild(), function (Form $form) use ($activity_id) {
            $form->display('id');

            $companies = Company::query()->get();
            $company_option = [];
            foreach ($companies as $com) {
                $company_option[$com->id] = $com->name;
            }

            $form->select('company_id')
                ->default($activity_id)
                ->options($company_option)
                ->required();
            $form->text('name')->required();

            $form->html(view('coordinate'), '区域选择'); // 加载自定义地图
            $form->hidden('map_points', '经纬度'); // 隐藏域，用于接收坐标点（这里如果想数据回填可以，->value('49.121221,132.2321312')）
            $form->hidden('map_area', '区域详细地址'); // 隐藏域，用于接收详细点位地址 ，必须是这个名字，js里写了

            $form->mobile('mobile', '联系电话');
            $form->image('wx_pic', '联系微信二维码')->autoUpload();

            $form->display('created_at');
            $form->display('updated_at');

            $form->tools(function (Form\Tools $tools) {
                // 去掉跳转列表按钮
                $tools->disableList();

                // 添加一个按钮, 参数可以是字符串, 匿名函数, 或者实现了Renderable或Htmlable接口的对象实例
                $tools->append('<a class="btn btn-sm btn-danger" href="/admin/company">&nbsp;&nbsp;返回机构管理</a>');
            });

        });
    }

    public function customMap()
    {
        return view('map');
    }
}
