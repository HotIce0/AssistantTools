<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/**
 * 小程序登陆认证,建立会话（无任何权限限制）
 * @author SaoGuang
 */
Route::get('login', 'MiniProgram\Auth\LoginController@index');

/**
 * 小程序用户绑定平台账号（需要登陆，学生教师都可以访问）
 * @author SaoGuang
 */
Route::group(['prefix' => 'bind'], function() {
    Route::get('getBindStatus', 'MiniProgram\Bind\BindController@getBindStatus');
    Route::post('bind', 'MiniProgram\Bind\BindController@bind');
});

/**
 * 小程序学生用户使用个人课程表
 * @author Sao Guang
 */
Route::group(['prefix' => 'courseTable'], function (){
    /*
     * 需要普通会员权限（学生教师都可访问）
     */
    Route::post('getPersonalCourseTable', 'MiniProgram\PersonalCourseTableController@getPersonalCourseTable');
});

/**
 * 基本信息获取接口路由
 * @author Sao Guang
 */
Route::group(['prefix' => 'basicInfo'], function (){
    /**
     * 没有任何认证(无需登陆)（学生教师都可以访问）
     */
    Route::get('colleges', 'BasicInfo\BasicInfoController@getColleges');
    Route::get('majors', 'BasicInfo\BasicInfoController@getMajors');
    Route::get('class', 'BasicInfo\BasicInfoController@getClass');
    Route::get('getYearTermRange', 'BasicInfo\BasicInfoController@getYearTermRange');
    Route::get('getCurrentYearTerm', 'BasicInfo\BasicInfoController@getCurrentYearTerm');
    Route::get('getCurrentWeekthWeek', 'BasicInfo\BasicInfoController@getCurrentWeekthWeek');
    /**
     * 需要登陆，并且已经绑定
     */
    Route::get('getPermissions', 'BasicInfo\BasicInfoController@getPermissions');
    /*
     * 需要管理员权限（学生教师都可以访问）
     */
    Route::get('updataSchoolStartDate', 'BasicInfo\BasicInfoController@updataSchoolStartDate');
});

/**
 * 小程序考勤模块
 * @author Sao Guang
 */
Route::group(['prefix' => 'attendanceRecord'], function (){
    /*
     * 需要班级管理员权限（只有学生可以访问）
     */
    Route::post('isAttendanceRecordExist', 'MiniProgram\AttendanceRecord\AttendanceRecordController@isAttendanceRecordExist');
    Route::post('generateAttendanceRecord', 'MiniProgram\AttendanceRecord\AttendanceRecordController@generateAttendanceRecord');
    Route::post('getAttendanceRecord', 'MiniProgram\AttendanceRecord\AttendanceRecordController@getAttendanceRecord');
    Route::post('saveAttendanceRecord', 'MiniProgram\AttendanceRecord\AttendanceRecordController@saveAttendanceRecord');
    //需要拥有查询考勤统计数据的权限 11
    Route::post('queryAttendanceRecordStatisticalData', 'MiniProgram\AttendanceRecord\AttendanceRecordController@queryAttendanceRecordStatisticalData');
});

/**
 * 需要普通会员权限（学生教师都可以访问）
 * 小程序图片上传模块
 */
Route::post('uploadImgFile', 'MiniProgram\UploadImgFileController@uploadImgFile');
Route::post('getDownloadImgFileUrl', 'MiniProgram\UploadImgFileController@getDownloadImgFileUrl');

/**
 * 测试路由
 */
Route::get('test', 'BasicInfo\BasicInfoController@test');
Route::get('test1', 'MiniProgram\AttendanceRecord\AttendanceRecordController@savaAttendanceRecord');