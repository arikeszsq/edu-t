<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\Facades\Cache;

class RobotController extends UserController
{
    public $title = '机器人库';


    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->model()->where('is_real', 2);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '用户昵称');
            $grid->column('avatar', '头像')->image(env('IMG_SERVE'), '100%', '40');
            $grid->column('created_at')->sortable();
            $grid->disableActions();//禁用所有操作
        });
    }


    protected function form()
    {
        return Form::make(new User(), function (Form $form) {
            $form->display('id');
            $form->text('name', '用户昵称');
            $form->file('avatar', '头像')->autoUpload();
            $form->display('created_at');
            $form->display('updated_at');

            $form->hidden('is_real');
            $form->hidden('nick_name');
            $form->saving(function (Form $form) {
                $form->is_real = 2;
                $form->nick_name = $form->input('name');
            });

        });
    }


}
