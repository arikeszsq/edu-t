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

class BeGrouper extends RowAction
{
    /**
     * @return string
     */
    protected $title = '<span class="btn btn-sm btn-warning" style="margin-left: 5px;">升为团长</span>';

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
        $activity_sign_user = ActivitySignUser::getActivitySignUserById($id);
        $user = User::query()->find($activity_sign_user->user_id);

        $data = [
            'leader_id' => $activity_sign_user->user_id,
            'leader_wx_name' => $user->name,
            'leader_boy_name' => $user->name,
        ];
        $new_group_id = ActivityGroup::query()->where('id',$activity_sign_user->group_id)->update($data);

        ActivitySignUser::query()->where('id', $id)->update([
            'grouper_name' => $user->id,
            'updated_at' => date('Y-m-d H:i:s', time()),
            'role' => 1,//1 团长
        ]);

        return $this->response()
            ->success('升为团长: ' . $this->getKey())
            ->redirect('/');
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确定?', '您确定要将用户升为团长么？'];
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
