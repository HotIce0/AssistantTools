<?php

namespace App\Http\Controllers\MiniProgram\Bind;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\User;
use Illuminate\Http\Request;

class BindController extends Controller{

    public function getBindStatus(Request $request){
        $res = Session::checkLogin($request);
        if($res['code'] == -1){
            //登陆验证失败
            return response($res);
        }
        //登陆验证通过

        $skey = Session::getSKeyFromRequest($request);

        //获取Session记录
        $session = Session::findUserInfoBySKey($skey);

        //查询用户绑定状态
        $user = User::where('session_id', '=', $session->session_id)
            ->first();

        if(empty($user)){
            return response([
                'code' => 0,
                'bindstatus' => false,
            ]);
        }else{
            return response([
                'code' => 0,
                'bindstatus' => true,
            ]);
        }

    }
}
