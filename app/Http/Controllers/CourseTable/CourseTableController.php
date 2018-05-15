<?php
//暂时不用。用于课表时间的解析
namespace App\Http\Controllers\CourseTable;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataFetch\DataFetchController;
use App\Models\Course;
use App\Models\ItemSetInfo;

class CourseTableController extends Controller{

    public function updateMyCourseTableData()
    {
        $user_id = 5;

        $dataFatch = new DataFetchController('14162400891', '43092119971118577X');
        $coursesData = $dataFatch->getPersonalCourseTableData();

        if (!$this->saveCoursesDataToDB($coursesData, $user_id)) {
            return '课程表数据存储失败';
        }

        return '课程表数据更新成功';
    }


    /**
     * 将coursesData解析后，存储至数据库
     * @param $coursesData
     * @param $user_id
     * @return bool|string 成功时，返回值为true，失败时，返回值为错误提示内容
     * @author Sao Guang
     */
    private function saveCoursesDataToDB($coursesData, $user_id)
    {
        //获取学年和学期
        $year = $coursesData['year'];
        $term = $coursesData['term'];

        //存储学年信息
        $this->saveSchoolYearToDB($year);

        //每天五节，每周七天。35节
        for ($i = 0; $i < 35; $i++) {
            if (!empty($coursesData[$i])) {
                foreach ($coursesData[$i] as $course) {
                    //解析周次
                    $weekths = $this->parseWeekth($course[2]);
                    foreach ($weekths as $weekth){
                        $courseDB = new Course();
                        $courseDB->course_name = $course[0];
                        $courseDB->teacher_name = $course[1];
                        $courseDB->position = $course[3];
                        $courseDB->school_year = $year;
                        $courseDB->school_term = $term;
                        $courseDB->weekth = $weekth;
                        //星期
                        $courseDB->week = ($i % 7) + 1;
                        //节次
                        $courseDB->section = (int)($i / 7 + 1);
                        //用户ID
                        $courseDB->user_id = $user_id;
                        if(!$courseDB->save()){
                            return '数据存储失败';
                        }
                    }
                }
            }
        }
        return true;
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