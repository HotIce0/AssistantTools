<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::group(['prefix' => 'miniprogram'], function(){
    /**
     * 小程序登陆认证,建立会话
     */
    Route::any('login', 'MiniProgram\Auth\LoginController@index');


    /**
     * 小程序用户绑定平台账号
     */
    Route::group(['prefix' => 'bind'], function() {
        Route::get('getBindStatus', 'MiniProgram\Bind\BindController@getBindStatus');
    });

    Route::get('textRequest', function(\Illuminate\Http\Request $request) {
        $res = \App\Models\Session::checkLogin($request);
        return response($res);
    });
});
