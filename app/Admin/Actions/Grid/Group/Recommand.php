<?php

namespace App\Admin\Actions\Grid\Group;

use App\Models\Activity;
use App\Models\ActivityGroup;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Recommand extends RowAction
{
    /**
     * @return string
     */
    protected $title = '推荐';

    public function __construct($title = null)
    {
        if ($title) {
            $this->title = $title;
        }
        parent::__construct($title);
    }

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $id = $this->getKey();
        $activity_group = ActivityGroup::getGroupById($id);
        $is_recommand = $activity_group->is_recommand;
        if ($is_recommand == 1) {
            ActivityGroup::query()->where('id', $id)->update(['is_recommand' => 2]);
        } else {
            ActivityGroup::query()->where('id', $id)->update(['is_recommand' => 1]);
        }
        return $this->response()
            ->success('Processed successfully: ' . $this->getKey())
            ->refresh();
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        $status = $this->row->is_recommand;
        if ($status == 1) {
            $title = '不推荐';
        } else {
            $title = '推荐';

        }
        return [
            "团：" . $this->row->title,
            "您确定要" . $title . "吗？",
        ];
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
