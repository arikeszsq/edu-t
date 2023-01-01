<?php

namespace App\Admin\Actions\Grid\Manage;

use App\Models\ActivityGroup;
use App\Models\ActivitySignUser;
use App\Models\User;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RowRefund extends RowAction
{
    /**
     * @return string
     */
    protected $title = '<span class="btn btn-sm btn-info" style="margin-left: 5px; margin-top: 2px;">退款</span>';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        // dump($this->getKey());
        $id = $this->getKey();

        return $this->response()
            ->success('退款: ' . $this->getKey())
            ->redirect('/');
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确定?', '您确定要退款么？'];
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
