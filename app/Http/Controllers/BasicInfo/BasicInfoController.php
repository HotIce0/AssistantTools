<?php

namespace App\Http\Controllers\BasicInfo;

use App\Http\Controllers\Controller;
use App\Models\ClassDB;
use App\Models\College;
use App\Models\Major;
use Illuminate\Http\Request;

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
}