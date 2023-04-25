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

class NewGroupDeal extends RowAction
{
    /**
     * @return string
     */
    protected $title = '<span class="btn btn-sm btn-success" style="margin-left: 5px;">独立成团</span>';

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
        $old_activity_group = ActivityGroup::query()->find($activity_sign_user->group_id);

        $ret = ActivityGroup::query()->where('id', $activity_sign_user->group_id)->update([
            'current_num' => ($old_activity_group->current_num - 1),
            'finished' => 2,
        ]);

        $user = User::query()->find($activity_sign_user->user_id);

        $data = [
            'activity_id' => $old_activity_group->activity_id,
            'name' => ActivityGroup::getOnlyCode($old_activity_group->activity_id),
            'num' => 1,
            'current_num' => 1,
            'leader_id' => $activity_sign_user->user_id,
            'leader_wx_name' => $user->name,
            'leader_boy_name' => $user->name,
            'creater_id' => $activity_sign_user->user_id,
            'finished' => 1,
            'success_time' => date('Y-m-d H:i:s', time()),
            'status' => 1,
        ];

        $new_group_id = ActivityGroup::query()->insertGetId($data);

        ActivitySignUser::query()->where('id', $id)->update([
            'group_id' => $new_group_id,
            'grouper_id' => $user->id,
            'updated_at' => date('Y-m-d H:i:s', time()),
            'role' => 1,//1 团长
        ]);

        return $this->response()
            ->success('独立成团: ' . $this->getKey())
            ->redirect('/');
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确定?', '您确定要独立成团么？'];
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
