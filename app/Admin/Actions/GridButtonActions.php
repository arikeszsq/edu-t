<?php


namespace App\Admin\Actions;


use Dcat\Admin\Grid\Displayers\Actions;

class GridButtonActions extends Actions
{
    /**
     * 修改 config/admin.php 文件中的 grid 配置：
     * //'grid_action_class' => Dcat\Admin\Grid\Displayers\DropdownActions::class,
     *'grid_action_class' => App\Admin\Actions\GridButtonActions::class,
     * @return string
     */
    protected function getViewLabel()
    {
        $label = trans('admin.show') . '👁查看';
        return '<span class="text-success">' . $label . '</span> &nbsp;';
    }

    /**
     * @return string
     */
    protected function getEditLabel()
    {
        $label = trans('admin.edit') . '🖊编辑';

        return '<span class="text-primary">' . $label . '</span> &nbsp;';
    }

    /**
     * @return string
     */
    protected function getQuickEditLabel()
    {
        $label = trans('admin.edit') . '⚡';
        $label2 = trans('admin.quick_edit');

        return '<span class="text-blue-darker" title="' . $label2 . '">' . $label . '</span> &nbsp;';
    }

    /**
     * @return string
     */
    protected function getDeleteLabel()
    {
        $label = trans('admin.delete') . '♻删除';

        return '<span class="text-danger">' . $label . '</span> &nbsp;';
    }
}
