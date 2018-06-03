<?php

namespace App\Models;
//By Sao Guang
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $table = 't_attendance_record';

    protected $primaryKey = 'attendance_record_id';

    /**
     * 按学院，考勤记录统计
     * @param $college_id
     * @param $school_year
     * @param $school_term
     * @param $weekth
     * @return array leavers_num_sum, absenteeism_num_sum, should_attendance_people_num
     */
    public static function attendanceRecordStatisticsByCollegeID($college_id, $school_year, $school_term, $weekth){
        $results = array();
        $results['leavers_num_sum'] = 0;
        $results['absenteeism_num_sum'] = 0;
        $results['should_attendance_people_num'] = 0;

        $majors = Major::where('college_id', '=', $college_id)
            ->select('major_id')
            ->get()
            ->toArray();
        $count = count($majors);
        for($i = 0; $i < $count; $i++){
            $res = AttendanceRecord::attendanceRecordStatisticsByMajorID($majors[$i]['major_id'], $school_year, $school_term, $weekth);
            $results['leavers_num_sum'] += $res['leavers_num_sum'];
            $results['absenteeism_num_sum'] += $res['absenteeism_num_sum'];
            $results['should_attendance_people_num'] += $res['should_attendance_people_num'];
        }
        return $results;
    }

    /**
     * 按专业，考勤记录统计
     * @param $majorID
     * @param $school_year
     * @param $school_term
     * @param $weekth
     * @return array leavers_num_sum, absenteeism_num_sum, should_attendance_people_num
     */
    public static function attendanceRecordStatisticsByMajorID($majorID, $school_year, $school_term, $weekth){
        $results = array();
        $results['leavers_num_sum'] = 0;
        $results['absenteeism_num_sum'] = 0;
        $results['should_attendance_people_num'] = 0;

        $class_s = ClassDB::where('major_id', '=', $majorID)
            ->select('class_id')
            ->get()
            ->toArray();
        $count = count($class_s);
        for($i = 0; $i < $count; $i++){
            $res = AttendanceRecord::attendanceRecordStatisticsByClassID($class_s[$i]['class_id'], $school_year, $school_term, $weekth);
            $results['leavers_num_sum'] += $res['leavers_num_sum'];
            $results['absenteeism_num_sum'] += $res['absenteeism_num_sum'];
            $results['should_attendance_people_num'] += $res['should_attendance_people_num'];
        }
        return $results;
    }

    /**
     * 按班级，考勤记录统计
     * @param $class_id
     * @param $school_year
     * @param $school_term
     * @param $weekth
     * @return array leavers_num_sum, absenteeism_num_sum, should_attendance_people_num
     */
    public static function attendanceRecordStatisticsByClassID($class_id, $school_year, $school_term, $weekth){
        //获取班级人数，用于计算
        $res = array();
        $class_person_num = ClassDB::find($class_id)->class_person_num;
        //查询旷课人数，请假人数，以及课节数
        $query = CourseAttendanceRecord::join('t_attendance_record', 't_course_attendance_record.course_id', '=', 't_attendance_record.course_id')
            ->where([
                ['class_id', '=', $class_id],
                ['school_year', '=', $school_year],
                ['school_term', '=', $school_term],
                ['weekth', '=', $weekth],
                ['attendance_record_status', '!=', '3'],//不是因为假期取消的，都算。
            ]);
        $course_count = intval($query->count());
        $res['leavers_num_sum'] = intval($query->sum('leavers_num'));
        $res['absenteeism_num_sum'] = intval($query->sum('absenteeism_num'));
        //应到人数
        $res['should_attendance_people_num'] = $course_count * $class_person_num;
        return $res;
    }
}