<?php

namespace App\Admin\Actions\Grid;

use App\Models\Activity;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BackToActivityList extends AbstractTool
{
    /**
     * @return string
     */
	protected $title = '返回活动列表';

	public function __construct($title = null)
    {
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
        $activity_id = Cache::get('activity_id');
        $activity = Activity::getActivityById($activity_id);
        if($activity->is_many == Activity::is_many_单商家){
            return $this->response()->redirect('/activity-one');
        }else{
            return $this->response()->redirect('/activity-many');
        }
    }

    /**
     * @return string|void
     */
    protected function href()
    {
        // return admin_url('auth/users');
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		// return ['Confirm?', 'contents'];
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
        return [
            'activity_id'=>request()->get('activity_id')
        ];
    }
}
