<?php

namespace App\Http\Controllers\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataFetch\DataFetchController;
use App\Models\Course;
use App\Models\ItemSetInfo;
use App\Models\Session;
use Illuminate\Http\Request;

/**
 * 个人课表获取控制器（可以获取学生或教师的个人课程表）
 * Class PersonalCourseTableController
 * @package App\Http\Controllers\MiniProgram
 * @author Sao Guang
 */
class PersonalCourseTableController extends Controller{
    /**
     * 获取个人课程表
     * @param Request $request
     * @return array|bool
     * @author Sao Guang
     */
    public function getPersonalCourseTable(Request $request){
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

        //是否是当前学年学期
        $isCurrentYearTerm = null;
        //是否需要最新的
        $needLatest = $request->needLatest == null ? false : true;
        //保持都为null，或者都不为null
        if($request->year == null or $request->term == null){
            $request->year = null;
            $request->term = null;
            //当前学年学期
            $isCurrentYearTerm = true;
        }else{
            //指定学年学期
            $isCurrentYearTerm = false;
        }

        if($needLatest){
            //更新个人课程表
            $retur = Course::updatePersonalCourseTableData($user->id, $request->year, $request->term);
            if($retur !== true){
                return [
                    'code' => 0,
                    'error' => $retur,
                ];
            }
        }

        //查询课程表数据（从数据库）
        if($isCurrentYearTerm){
            $courses = Course::isPersonalCourseTableDataExist($user->id, ItemSetInfo::getYearNow(), ItemSetInfo::getTermNow());
        }else{
            $courses = Course::isPersonalCourseTableDataExist($user->id, $request->year, $request->term);
        }

        //查询个人课程表
        if($courses === false){
            //课程表信息不存在,需要爬取最新课表
            //更新个人课程表
            $retur = Course::updatePersonalCourseTableData($user->id, $request->year, $request->term);
            if($retur !== true){
                return [
                    'code' => 0,
                    'error' => $retur,
                ];
            }
            //再次查询课程表数据（从数据库）
            if($isCurrentYearTerm){
                $courses = Course::isPersonalCourseTableDataExist($user->id, ItemSetInfo::getYearNow(), ItemSetInfo::getTermNow());
            }else{
                $courses = Course::isPersonalCourseTableDataExist($user->id, $request->year, $request->term);
            }
        }

        return [
            'code' => 0,
            'courses' => json_encode($this->normalizeCourseTableData($courses)),
            'year' => $isCurrentYearTerm ? ItemSetInfo::getYearNow() : $request->year,
            'term' => $isCurrentYearTerm ? ItemSetInfo::getTermNow() : $request->term,
        ];
    }

    /**
     * 将从数据读取出来的数据，处理为按每天划分的数据
     * @param $courses
     * @return array
     */
    public function normalizeCourseTableData($courses){
        if(empty($courses))
            return [];
        $data = array();
        for ($i = 0; $i < 7; $i++){
            $data[$i + 1] = array();
            for($j = 0; $j < 5; $j++){
                $data[$i + 1][$j + 1] = array();
            }
        }

        foreach ($courses as $course){
            array_push($data[$course->week][$course->section], $course);
        }
        return $data;
    }
}