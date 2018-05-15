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

//Route::get('/getCourseTable', 'DataFetch\DataFetchController@index');
Route::get('/get', function (){
    $key = "LfjywyNGE81EHIQXiq/whA==";
    $accept = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
    return $accept;
});

Route::get('/updatePersonalCourseTableData', 'CourseTable\CourseTableController@updatePersonalCourseTableData');


Route::group(['prefix' => 'miniprogram'], function(){





    Route::get('textRequest', function(\Illuminate\Http\Request $request) {
        $res = \App\Models\Session::checkLogin($request);
        return response($res);
    });
});
