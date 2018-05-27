<?php

namespace App\Http\Controllers\MiniProgram;

use App\Models\Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class  UploadImgFileController extends Controller {
    /**
     * 图片上传
     * @param Request $request
     * @return array|bool|false|string
     */
    public function uploadImgFile(Request $request){
        //登陆验证
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }

        //判断是否绑定
        $user = Session::isBind($res['session']->session_id);
        if($user === false){
            return [
                'code' => 0,
                'error' => '未绑定平台账号',
            ];
        }

        //需要拥有图片上传的权限 权限编号 7
        if(!$user->hasPermission('7')){
            return apiFailResponse('您没有图片上传的权限');
        }

        $path = $request->file('imgfile')->store('public');
        return apiSuccessResponse($path);
    }

    /**
     * 文件路径获取url
     * @param Request $request
     */
    public function getDownloadImgFileUrl(Request $request){
        //登陆验证
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }

        //判断是否绑定
        $user = Session::isBind($res['session']->session_id);
        if($user === false){
            return [
                'code' => 0,
                'error' => '未绑定平台账号',
            ];
        }

        //需要拥有图片下载的权限 权限编号 8
        if(!$user->hasPermission('8')){
            return apiFailResponse('您没有图片下载的权限');
        }

        if($request->filePath){
            return apiSuccessResponse(asset($url = Storage::url($request->filePath)));
        }
        return apiFailResponse('参数不完整，缺少filePath');
    }
}