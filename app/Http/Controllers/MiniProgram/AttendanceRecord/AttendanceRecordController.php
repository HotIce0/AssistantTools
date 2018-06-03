<?php

namespace App\Http\Controllers\MiniProgram\AttendanceRecord;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassDB;
use App\Models\CourseAttendanceRecord;
use App\Models\Major;
use App\Models\Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Class AttendanceRecordController
 * @package App\Http\Controllers\MiniProgram\AttendanceRecord
 * @author Sao Guang
 */
class AttendanceRecordController extends Controller{
    public function queryAttendanceRecordStatisticalData(Request $request){
        //查询学院考勤数据(按照每个专业进行数据查询)
        AttendanceRecord::attendanceRecordStatisticsByCollegeID(1, 2017, 2, 13);

        $college_id = 1;
        $majors = Major::where('college_id', '=', $college_id)
            ->select('major_id', 'major_name')
            ->get();
        dd($majors);
        //AttendanceRecord::where()
    }

    /**
     * 查询考勤记录
     * @param Request $request
     * @return array|bool|string
     */
    public function getAttendanceRecord(Request $request){
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

        //需要拥有获取考勤记录数据的权限 权限编号 5
        if(!$user->hasPermission('5')){
            return apiFailResponse('您没有获取考勤记录数据的权限');
        }

        //验证规则
        $rules = array(
            'year' => 'required|integer|min:1',
            'term' => 'required|integer|min:1|max:2',
            'weekth' => 'required|integer|min:1|max:30',
            'week' => 'required|integer|min:1|max:7',
        );
        //错误消息
        $message = array(
            'required'=>':attribute 必须填写',
            'integer' => ':attribute 必须是整数',
            'min' => ':attribute 必须为正整数',
            'term.max' => ':attribute 最大值为2',
            'weekth.max' => ':attribute 最大值为 30',
            'week.max' => ':attribute 最大值为7',
        );
        //字段意义
        $meaning = array(
            'year' => '学年',
            'term' => '学期',
            'weekth' => '周次',
            'week' => '星期',
        );
        //表单验证
        $validator = Validator::make($request->all(), $rules, $message, $meaning);
        if($validator->fails()){
            return apiFailResponse($validator->errors());
        }

        //查询考勤信息
        $arrayAttendanceData = $this->getAttendanceRecordData(
            $user->id,
            $request->year,
            $request->term,
            $request->weekth,
            $request->week
        )->toArray();

        return apiSuccessResponse($arrayAttendanceData);
    }

    /**
     * 获取考勤记录和考勤课程记录合并的数据
     * @param $user_id
     * @param $year
     * @param $term
     * @param $weekth
     * @param $week
     * @return mixed
     */
    private function getAttendanceRecordData($user_id, $year, $term, $weekth, $week){
        $attendanceData = CourseAttendanceRecord::join('t_attendance_record', 't_course_attendance_record.course_id', '=', 't_attendance_record.course_id')
            ->where([
                ['t_attendance_record.user_id', '=', $user_id],
                ['school_year', '=', $year],
                ['school_term', '=', $term],
                ['weekth', '=', $weekth],
                ['week', '=', $week]
            ])
            ->orderBy('attendance_record_status', 'asc')
            ->orderBy('section', 'asc')
            ->select([
                'attendance_record_id',
                'course_type',
                't_attendance_record.course_id',
                't_attendance_record.user_id',
                'attendance_record_status',
                'leavers_num',
                'leave_detail',
                'absenteeism_num',
                'absenteeism_detail',
                'mobile_num',
                'mobile_detail_picture_file_name',
                'course_name',
                'teacher_name',
                'position',
                'school_year',
                'school_term',
                'weekth',
                'week',
                'section',
            ])
            ->get();
        return $attendanceData;
    }

    /**
     * 指定学年，学期的 考勤记录是否存在
     * @param Request $request
     * @return array|bool|string
     */
    public function isAttendanceRecordExist(Request $request){
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

        //需要拥有判断考勤记录是否存在的权限 权限编号 3
        if(!$user->hasPermission('3')){
            return apiFailResponse('您没有判断考勤记录是否存在的权限');
        }

        //验证规则
        $rules = array(
            'year' => 'required|integer|min:1',
            'term' => 'required|integer|min:1'
        );
        //错误消息
        $message = array(
            'required'=>':attribute 必须填写',
            'integer' => ':attribute 必须是整数',
            'min' => ':attribute 不能为负数',
        );
        //字段意义
        $meaning = array(
            'year' => '学年',
            'term' => '学期',
        );
        //表单验证
        $validator = Validator::make($request->all(), $rules, $message, $meaning);
        if($validator->fails()){
            return apiFailResponse($validator->errors());
        }

        //判断是否已经存在
        if(CourseAttendanceRecord::isCourseTableDataExist($user->id, $request->year, $request->term) !== false){
            //已经存在数据
            return apiSuccessResponse(true);
        }else{
            return apiSuccessResponse(false);
        }
    }

    /**
     * 生成考勤记录
     * @param Request $request
     * @return array|bool|string
     */
    public function generateAttendanceRecord(Request $request){
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

        //需要拥有生成考勤记录数据的权限 权限编号 4
        if(!$user->hasPermission('4')){
            return apiFailResponse('您没有生成考勤记录数据的权限');
        }

        //验证规则
        $rules = array(
            'year' => 'required|integer|min:1',
            'term' => 'required|integer|min:1'
        );
        //错误消息
        $message = array(
            'required'=>':attribute 必须填写',
            'integer' => ':attribute 必须是整数',
            'min' => ':attribute 不能为负数',
        );
        //字段意义
        $meaning = array(
            'year' => '学年',
            'term' => '学期',
        );
        //表单验证
        $validator = Validator::make($request->all(), $rules, $message, $meaning);
        if($validator->fails()){
            return apiFailResponse($validator->errors());
        }

        //判断是否已经存在
        if(CourseAttendanceRecord::isCourseTableDataExist($user->id, $request->year, $request->term) !== false){
            //已经存在数据
            return apiFailResponse($request->year . '-' . $request->term . ' 考勤数据已经存在');
        }

        //生成考勤课程表数据(并生成考勤数据)
        $retr = $this->generateAttendanceRecordCourseData($user->id, $request->year, $request->term);
        if($retr !== true){
            return apiFailResponse($retr);
        }
        return apiSuccessResponse(null);
    }

    /**
     * 生成考勤课程表数据(并生成考勤数据)
     * @param $user_id
     * @param $year
     * @param $term
     * @return bool|string
     */
    private function generateAttendanceRecordCourseData($user_id, $year, $term){
        $retr = CourseAttendanceRecord::copyAndParsePersonalCourseGenerateARData($user_id, $year, $term);
        if($retr !== true){
            return $retr;
        }
        return true;
    }

    /**
     * 存储考勤记录(包括修改和考勤)
     * @param Request $request
     * @return array|bool|string
     */
    public function saveAttendanceRecord(Request $request){
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

        //需要拥有编辑考勤记录数据的权限 权限编号 6
        if(!$user->hasPermission('6')){
            return apiFailResponse('您没有编辑考勤记录数据的权限');
        }

        //验证规则
        $rules = array(
            'attendance_record_id' => 'required|exists:t_attendance_record,attendance_record_id',
            'leavers_num' => 'required|integer|min:0',
            'absenteeism_num' => 'required|integer|min:0',
            'mobile_num' => 'nullable|integer|min:0',
            'leave_detail' => 'nullable|string|max:512',
            'absenteeism_detail' => 'nullable|string:max:512',
            'img_file_path' => 'nullable|string',
        );
        //错误消息
        $message = array(
            'required'=>':attribute 必须填写',
            'integer' => ':attribute 必须是整数',
            'min' => ':attribute 不能为负数',
            'max' => ':attribute 不能超过512个字符',
            'string' => ':attribute 必须为字符串',
            'exist' => ':attribute 无效',
        );
        //字段意义
        $meaning = array(
            'attendance_record_id' => '考勤记录ID',
            'leavers_num' => '旷课人数',
            'leave_detail' => '旷课情况',
            'absenteeism_num' => '请假人数',
            'absenteeism_detail' => '请假情况',
            'mobile_num' => '手机上交台量',
            'img_file_path' => '手机上交情况图片地址',
        );
        //表单验证
        $validator = Validator::make($request->all(), $rules, $message, $meaning);
        if($validator->fails()){
            return apiFailResponse($validator->errors());
        }

        //存储考勤记录
        $attendanceRecord = AttendanceRecord::where('attendance_record_id', '=', $request->attendance_record_id)
            ->first();
        $attendanceRecord->leavers_num = $request->leavers_num;
        $attendanceRecord->leave_detail = $request->leave_detail;
        $attendanceRecord->absenteeism_num = $request->absenteeism_num;
        $attendanceRecord->absenteeism_detail = $request->absenteeism_detail;
        $attendanceRecord->mobile_num = $request->mobile_num;
        //如果有原图，把原图删除
        Storage::delete($attendanceRecord->mobile_detail_picture_file_name);
        $attendanceRecord->mobile_detail_picture_file_name = $request->img_file_path;
        $attendanceRecord->	attendance_record_status = '2';//考勤记录状态2代表已填写。
        if($attendanceRecord->save()){
            return apiSuccessResponse('考勤记录保存成功!');
        }else{
            return apiFailResponse('数据存储异常:考勤记录存储失败!');
        }
    }

    /**
     * 解析周次
     * @param $weekth
     * @return array
     */
    private function parseWeekth($weekth){
        $weekths = array();
        //获取单周还是双周
        $oddWeek = false;
        $pluralWeek = false;
        if(strpos($weekth, '单周') !== false){
            $oddWeek = true;
        }
        if(strpos($weekth, '双周') !== false){
            $pluralWeek = true;
        }
        //读取全部显示出的数字
        preg_match_all('/\d+/', $weekth, $matches);
        foreach ($matches[0] as $match){
            if($oddWeek && !$this->isOddNum($match))
                continue;
            if($pluralWeek && $this->isOddNum($match))
                continue;
            $weekths[$match] = intval($match);
        }
        //读取1-19这样的时间起始和终止
        $count = preg_match_all('/\d+(?=-)/', $weekth, $matches);
        $startArr = $matches[0];
        preg_match_all('/(?<=-)\d+/', $weekth, $matches);
        $endArr = $matches[0];
        //获取中间夹着的所有数字
        for($i = 0; $i < $count; $i++){
            for($j = $startArr[$i] + 1; $j < $endArr[$i]; $j++){
                if($oddWeek && !$this->isOddNum($j))
                    continue;
                if($pluralWeek && $this->isOddNum($j))
                    continue;
                $weekths[$j] = $j;
            }
        }
        return $weekths;
    }

    /**
     * 是否为单数
     * @param $num
     * @return bool
     */
    private function isOddNum($num){
        return $num % 2 != 0;
    }
}