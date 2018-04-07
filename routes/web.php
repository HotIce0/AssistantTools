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

Route::get('/set', function(\Illuminate\Http\Request $request){
    
    $cookie = Cookie::make('session_id', $request->session()->getId(), 120);
    return \Response::make('index')->withCookie($cookie);;
    $request->session()->put('sao', 'saoguang1');
});

Route::get('/get', function(\Illuminate\Http\Request $request){
    //dd ($request->headers);
    //return $request->session()->all();
    return $request->session()->get('sao');
});

Route::get('/getid', function(\Illuminate\Http\Request $request){
    return $request->session()->getId();
});
Route::get('/request', function(\Illuminate\Http\Request $request){
    dd($request);
});


Route::group(['prefix' => 'miniprogram'], function(){
    /*
     * 小程序登陆认证,建立会话
     */
    Route::any('login', 'MiniProgram\Auth\LoginController@index');

    Route::get('basic1', function() {

    });
});
