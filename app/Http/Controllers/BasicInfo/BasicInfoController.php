<?php

namespace App\Http\Controllers\BasicInfo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataFetch\DataFetchController;
use App\Models\ClassDB;
use App\Models\College;
use App\Models\ItemSetInfo;
use App\Models\Major;
use App\Models\Session;
use Illuminate\Http\Request;

/**
 * 基础信息获取接口
 * Class BasicInfoController
 * @package App\Http\Controllers\BasicInfo
 * @author Sao Guang
 */
class BasicInfoController extends Controller{
    /**
     * 获取学院信息
     * @param Request $request
     * @return array
     */
    public function getColleges(Request $request){
        $colleges = College::select(['college_id', 'college_identifier', 'college_name'])->get();

        return [
            'code' => 0,
            'colleges' => json_encode($colleges),
        ];
    }

    /**
     * 通过学院ID获取专业信息
     * @param Request $request
     * @return array
     */
    public function getMajors(Request $request){
        if($request->college_id === null){
            return [
                'code' => 0,
                'error' => '缺少参数学院ID:college_id',
            ];
        }

        $majors = Major::where('college_id', '=', $request->college_id)
            ->select(['major_id', 'major_identifier','major_name'])
            ->get();

        return [
            'code' => 0,
            'majors' => json_encode($majors),
        ];
    }

    /**
     * 通过专业信息获取班级ID
     * @param Request $request
     * @return array
     */
    public function getClass(Request $request){
        if($request->major_id === null){
            return [
                'code' => 0,
                'error' => '缺少参数专业ID:major_id',
            ];
        }

        $class = ClassDB::where('major_id', '=', $request->major_id)
            ->select(['class_id', 'class_identifier', 'class_name'])
            ->get();

        return [
            'code' => 0,
            'class' => json_encode($class),
        ];
    }

    /**
     * 获取学年学期的范围
     * @param Request $request
     * @return string
     */
    public function getYearTermRange(){
        $data = new \stdClass();
        $data->start = ItemSetInfo::getStartYearTerm();
        $data->end = ItemSetInfo::getEndYearTerm();
        return json_encode($data);
    }

    /**
     * 获取当前的学年和学期
     * @return string
     */
    public function getCurrentYearTerm(){
        $res = ItemSetInfo::getYearTerm();
        if($res === false)
            return apiFailResponse('当前学年学期数据无效，需要管理员更新');
        return apiSuccessResponse($res);
    }

    /**
     * 获取当前的周次和星期
     * @return string
     */
    public function getCurrentWeekthWeek(){
        $YMD = explode('-', date('Y-n-j'));
        $res = ItemSetInfo::yearTermYMDToWeekthWeek(ItemSetInfo::getYearNow(), ItemSetInfo::getTermNow(), $YMD);
        if($res === false){
            return apiFailResponse('无法获取到当前的周次和星期数据：管理员没有更新数据');
        }else{
            return apiSuccessResponse($res);
        }
    }

    /**
     * 更新学年学期，开学日期数据
     * @param Request $request
     * @return array|bool|string
     */
    public function updataSchoolStartDate(Request $request){
        //登陆验证
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }

        //判断是否绑定
        $user = Session::isBind($res['session']->session_id);
        if($user === false){
            return [
                'code' => 0,
                'error' => '未绑定平台账号',
            ];
        }

        //需要拥有更新学年学期的开学日期的权限 权限编号 2
        if(!$user->hasPermission('2')){
            return apiFailResponse('您没有更新学年学期的开学日期的权限');
        }

        //判断是学生还是教师
        $isStudent = $user->isStudent();
        $userInfo = $user->getUserInfo();
        $dataFatch = new DataFetchController($userInfo->user_job_id, $userInfo->id_card_no);
        //如果是教师，一体化平台密码没有完善将无法进行数据更新
        if(!$isStudent){
            if($userInfo->school_site_password_bkjw == null){
                return apiFailResponse('无法获取您的课程表 : 您未完善您学校一体化平台密码或者您已经更改密码');
            }
            $dataFatch->default_password = $userInfo->school_site_password_bkjw;
        }
        //开始更新数据
        $retr = $dataFatch->updataSchoolStartDate($isStudent);
        if($retr !== true){
            return apiFailResponse($retr);
        }else{
            return apiSuccessResponse('各学年学期，开学日期更新完毕!');
        }
    }

    /**
     * 获取所有的权限
     * @param Request $request
     * @return array|bool|string
     */
    public function getPermissions(Request $request){
        //登陆验证
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }

        //判断是否绑定
        $user = Session::isBind($res['session']->session_id);
        if($user === false){
            return [
                'code' => 0,
                'error' => '未绑定平台账号',
            ];
        }

        return apiSuccessResponse($user->getPermissions());
    }
}