<?php

namespace App\Admin\Forms;

use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivitySignCom;
use App\Models\ActivitySignUser;
use App\Models\Company;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;

class GroupList extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $id = $this->payload['id'] ?? null;
        $group_id = $input['group_id'] ?? null;

        $row = ActivitySignUser::query()->find($id);
        $old_group = ActivityGroup::query()->find($row->group_id);
        $ret = ActivityGroup::query()->where('id', $row->group_id)->update([
            'current_num' => ($old_group->current_num - 1),
        ]);

        if ($ret) {
            $new_group = ActivityGroup::query()->find($group_id);
            $new_current_num = ($new_group->current_num + 1);
            ActivityGroup::query()->where('id', $group_id)->update([
                'current_num' => $new_current_num,
            ]);
            if ($new_current_num == $new_group->num) {
                ActivityGroup::query()->where('id', $group_id)->update([
                    'finished' => 1,
                ]);
            }
            ActivitySignUser::query()->where('id', $id)->update([
                'group_id' => $group_id,
                'grouper_id' => ActivityGroup::getGroupLeaderIDByGroupId($group_id),
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
        }

        return $this
            ->response()
            ->success('成功')
            ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $options = ActivityGroup::getUnDealList();
        $this->confirm('您确定移团么', '');
        $this->select('group_id', '团')->options($options)->required();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'name' => 'John Doe',
            'email' => 'John.Doe@gmail.com',
        ];
    }
}
