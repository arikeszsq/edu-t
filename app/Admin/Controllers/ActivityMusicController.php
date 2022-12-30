<?php

namespace App\Admin\Controllers;

use App\Models\ActivityMusic;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActivityMusicController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ActivityMusic(), function (Grid $grid) {
            $grid->quickSearch('name')->placeholder('搜索名称');
            $grid->column('id')->sortable();
            $grid->column('name');
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
        return Show::make($id, new ActivityMusic(), function (Show $show) {
            $show->field('id');
            $show->field('name');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ActivityMusic(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->file('file')->autoUpload()->required()->accept('MP3,AAC,Ogg Vorbis,Opus,WAV,FLAC,APE,ALAC,WavPack,mp3');
        });
    }
}
