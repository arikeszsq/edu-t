<?php


namespace App\Http\Controllers;


use App\Http\Services\ActivityService;
use App\Http\Traits\AreaTrait;
use App\Http\Traits\ImageTrait;
use App\Models\ActivityFormField;
use App\Models\Company;
use App\Models\CompanyChild;
use App\Models\CompanyCourse;
use App\Models\Share;
use App\Models\UserViewCount;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

class CompanyController extends Controller
{
    use AreaTrait;
    use ImageTrait;

    /**
     * 根据机构id,获取机构详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function detail(Request $request)
    {
        try {
            $inputs = $request->all();
            $activity_id = $inputs['activity_id'];
            $id = $inputs['id'];
            $user = self::authUser();
            $company = Company::query()->find($id);
            $schools = CompanyChild::getAllSchoolsByCompanyId($id);
            $child = [];
            foreach ($schools as $school) {
                $child[] = [
                    'name' => $school->name,
                    'map_area' => $school->map_area,
                    'gap' => $this->getDistance($user->map_points, $school->map_points),
                ];
            }
            $courses = CompanyCourse::getAllCourseByCompanyId($id);
            $course_array = [];
            foreach ($courses as $course) {
                $course_array[] = [
                    'name' => $course->name,
                    'logo_c' => $this->fullImgUrl($course->logo_c),
                    'sign_num' => CompanyCourse::getSignNumByCourseId($activity_id, $course->id)
                ];
            }

            $data = [];
            $data['company_name'] = $company->name;
            $data['intruduction'] = $company->intruduction;
            $data['contacter'] = $company->contacter;
            $data['mobile'] = $company->mobile;
            $data['schools'] = $child;
            $data['courses'] = $course_array;
            return self::success($data);
        } catch (Exception $e) {
            return self::error($e->getCode(), $e->getMessage());
        }
    }


}
