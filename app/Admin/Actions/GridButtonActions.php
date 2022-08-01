<?php


namespace App\Admin\Actions;


use Dcat\Admin\Grid\Displayers\Actions;

class GridButtonActions extends Actions
{
    /**
     * @return string
     */
    protected function renderView()
    {
        $label = trans('admin.show');

        return <<<EOT
<a href="{$this->resource()}/{$this->getKey()}" title="{$label}">
    <i class="feather icon-eye grid-action-icon"></i>详细
</a>&nbsp;
EOT;
    }

    /**
     * @return string
     */
    protected function renderEdit()
    {
        $label = trans('admin.edit');

        return <<<EOT
<a href="{$this->resource()}/{$this->getKey()}/edit" title="{$label}">
    <i class="feather icon-edit-1 grid-action-icon"></i>$label
</a>&nbsp;
EOT;
    }

    /**
     * @return string
     */
    protected function renderDelete()
    {
        $label = trans('admin.delete');

        return <<<EOT
<a title="{$label}" href="javascript:void(0);" data-message="ID - {$this->getKey()}" data-url="{$this->resource()}/{$this->getKey()}" data-action="delete">
    <i class="feather icon-trash grid-action-icon"></i>$label
</a>&nbsp;
EOT;
    }
}
