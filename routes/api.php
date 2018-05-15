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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 小程序登陆认证,建立会话
 * @author SaoGuang
 */
Route::get('login', 'MiniProgram\Auth\LoginController@index');

/**
 * 小程序用户绑定平台账号
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
    Route::post('getPersonalCourseTable', 'MiniProgram\PersonalCourseTableController@getPersonalCourseTable');
});

