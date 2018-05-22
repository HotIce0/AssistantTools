<?php

namespace App\Http\Controllers\MiniProgram\AttendanceRecord;

use App\Http\Controllers\Controller;
use App\Models\CourseAttendanceRecord;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller{
    //查询考勤记录
    public function getAttendanceRecord(){
        $user_id = 1;
        $year = 2017;
        $term = 1;
        $weekth = 1;
        $week = 1;
        //查询用于记录考勤信息的课表数据是否存在
        $coursesData = CourseAttendanceRecord::getCourseTableData($user_id, $year, $term, $weekth, $week);
        if($coursesData === false){
            //数据不存在
        }else{

        }

    }

    public function updataAttendanceRecordCourseData($user_id, $year, $term){
        $retr = CourseAttendanceRecord::copyAndParsePersonalCourse($user_id, $year, $term);
        if($retr !== true){
            return $retr;
        }
        return true;
    }

    //存储考勤记录
    public function savaAttendanceRecord(){

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