<?php

namespace App\Models;
//By Sao Guang
use App\Http\Controllers\DataFetch\DataFetchController;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * 个人课程表 模型
 * Class Course
 * @package App\Models
 */
class Course extends Model
{
    protected $table = 't_course';

    protected $primaryKey = 'course_id';

    /**
     * 更新个人课程表数据
     * @param $user_id
     * @param null $year
     * @param null $term
     * @param string $weekNum
     * @return bool|string
     * @author Sao Guang
     */
    public static function updatePersonalCourseTableData($user_id, $year = null, $term = null, $weekNum = ''){
        $student_id = null;
        $card_id = null;

        //查询个人信息(获取学号以及身份证号码)
        $user = User::find($user_id);
        if($user == null){
            return '用户ID无效';
        }

        $userInfo = $user->getUserInfo();
        $student_id = $userInfo->user_job_id;
        $card_id = $userInfo->id_card_no;
        $isStudent = $user->isStudent();

        //从学院网站解析个人课程表数据。
        $dataFatch = new DataFetchController($student_id, $card_id);
        //教师密码无法重置。必须读取教师密码登陆
        if(!$isStudent){
            if($userInfo->school_site_password_bkjw == null){
                return '无法获取您的课程表 : 您未完善您学校一体化平台密码或者您已经更改密码';
            }
            $dataFatch->default_password = $userInfo->school_site_password_bkjw;
        }
        $coursesData = $dataFatch->getPersonalCourseTableData($year, $term, $weekNum, $isStudent);
        if(!is_array($coursesData)){
            return $coursesData;
        }

        //更新现在的学年和学期
        if($year === null and $term === null){
            ItemSetInfo::setYearTerm($coursesData['year'], $coursesData['term']);
        }

        //清除个人原有课程表数据(无返回值)
        Course::cleanPersonalCourseTableDB($user_id, $coursesData['year'], $coursesData['term']);

        //存储个人课程表数据。
        if(Course::savePersonalCourseTableData($coursesData, $user_id) !== true){
            return '数据库存储个人课程表数据失败';
        }
        return true;
    }

    /**
     * 保存个人课程表数据
     * @param $coursesData
     * @param $user_id
     * @return bool|string
     * @author Sao Guang
     */
    private static function savePersonalCourseTableData($coursesData, $user_id){
        //获取学年和学期
        $year = $coursesData['year'];
        $term = $coursesData['term'];

        //存储学年信息
        if(Course::saveSchoolYearToDB($year) !== true){
            return '存储学年信息错误';
        }

        //解析数据，存储到数据库

        //每天五节，每周七天。35节
        for($i = 0; $i < 35; $i++){
            if (!empty($coursesData[$i])) {
                foreach ($coursesData[$i] as $course) {
                    //解析周次
                    $courseDB = new Course();
                    $courseDB->course_name = $course[0];
                    $courseDB->teacher_name = $course[1];
                    $courseDB->position = $course[3];

                    $courseDB->school_year = $year;
                    $courseDB->school_term = $term;
                    $courseDB->weekth = $course[2];
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
        return true;
    }
    /**
     * 保存学年信息到数据库
     * @param $year
     * @return bool|string
     * @author Sao Guang
     */
    private static function saveSchoolYearToDB($year){
        $item = ItemSetInfo::where('item_content_id', '=', $year)->first();
        if ($item == null) {
            $newYearItem = new ItemSetInfo();
            $newYearItem->item_no = 3;
            $newYearItem->item_content_id = $year;
            $newYearItem->item_content = $year;
            $newYearItem->sort_id = 0;
            if (!$newYearItem->save())
                return '创建学年数据失败';
            else
                return true;
        }
        return true;
    }

    /**
     * 清除原个人课程表数据
     * @param $user_id
     * @param null $year
     * @param null $term
     */
    private static function cleanPersonalCourseTableDB($user_id, $year = null, $term = null){
        $rule = [
            ['user_id', '=', $user_id]
        ];
        if($year !== null)
            array_push($rule, ['school_year', '=', $year]);
        if($term !== null)
            array_push($rule, ['school_term', '=', $term]);
        Course::where($rule)
            ->delete();
    }

    /**
     * 个人课程表数据是否存在
     * @param $user_id
     * @return bool|$courses 如果课程表存在就返回课程表数据，否则返回false
     */
    public static function isPersonalCourseTableDataExist($user_id, $year, $term){
        $rule = [
            ['user_id', '=', $user_id],
            ['school_year', '=', $year],
            ['school_term', '=', $term]
        ];
        $courses = Course::where($rule)
            ->get();
        if($courses->isEmpty())
            return false;
        else
            return $courses;
    }
}