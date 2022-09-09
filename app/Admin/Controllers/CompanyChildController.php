<?php

namespace App\Admin\Controllers;

use App\Models\CompanyChild;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CompanyChildController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CompanyChild(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('company_id');
            $grid->column('name');
            $grid->column('address');
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
        return Show::make($id, new CompanyChild(), function (Show $show) {
            $show->field('id');
            $show->field('company_id');
            $show->field('name');
            $show->field('address');
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
        return Form::make(new CompanyChild(), function (Form $form) {
            $form->display('id');
            $form->text('company_id');
            $form->text('name');
            $form->text('address');

//            Form\Field\Map::requireAssets();
            $latitude = 'latitude';
            $longitude = 'longitude';
            $label = '地图控件';

            $form->map($latitude, $longitude, $label);

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
