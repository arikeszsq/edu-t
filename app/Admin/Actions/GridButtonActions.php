<?php


namespace App\Admin\Actions;


use Dcat\Admin\Grid\Displayers\Actions;

class GridButtonActions extends Actions
{
    /**
     * @return string
     */
    protected function getViewLabel()
    {
        $label = trans('admin.show') . '👁';
        return '<span class="text-success">' . $label . '</span> &nbsp;';
    }

    /**
     * @return string
     */
    protected function getEditLabel()
    {
        $label = trans('admin.edit') . '🖊';

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
        $label = trans('admin.delete') . '♻';

        return '<span class="text-danger">' . $label . '</span> &nbsp;';
    }

}
