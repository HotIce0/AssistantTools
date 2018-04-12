<?php

namespace App\Http\Controllers\MiniProgram\Bind;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\User;
use Illuminate\Http\Request;

class BindController extends Controller{
    /**
     * 获取绑定状态的API
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * 绑定平台账号API
     * @param Request $request
     * @return array
     * 绑定成功: {"code": 0}
     * 绑定失败: {"code": -1, "error": "原因内容"} (原因内容：1.用户名与密码不匹配 2.用户名不存在)
     */
    public function bind(Request $request){
        $jobId = $request->jobId;
        $idCardNo = $request->idCardNo;
        return 'sao';
    }
}
