<?php

namespace App\Http\Controllers\MiniProgram\Bind;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class BindController
 * @package App\Http\Controllers\MiniProgram\Bind
 * @author Sao Guang
 */
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
     * 绑定平台账号API(允许覆盖绑定，即可以覆盖平台用户原来绑定的微信账号)
     * @param Request $request
     * @return array
     * 绑定成功: {"code": 0}
     * 绑定失败: {"code": -1, "error": "原因内容"} (原因内容：1.用户名与密码不匹配 2.用户名不存在 3.用户名或密码不完整) 以及登陆状态验证返回值
     */
    public function bind(Request $request){
        //登陆状态验证
        $res = Session::checkLogin($request);
        if($res['code'] == -1){
            //登陆验证失败
            return response($res);
        }

        //检验参数完整性
        $userName = $request->userName;
        $password = $request->password;
        if(empty($userName) or empty($password))
            return response([
                "code" => 0,
                "error" => '用户名或密码不完整',
            ]);

        //获取skey
        $skey = Session::getSKeyFromRequest($request);

        //获取Session记录
        $session = Session::findUserInfoBySKey($skey);

        //根据核对userName 和 password 即平台账号密码
        $user = User::findUserByJobId($userName);
        if($user === null)
            return response([
                "code" => 0,
                "error" => '用户名不存在',
            ]);

        //核对密码 check password
        if(!Hash::check($password, $user->password)) {
            return response([
                "code" => 0,
                "error" => '用户名与密码不匹配',
            ]);
        }

        //密码匹配，进行绑定操作
        $user->session_id = $session->session_id;
        if($user->save()){
            //绑定成功
            return response([
                "code" => 0,
            ]);
        }else{
            return response([
                "code" => 0,
                "error" => '绑定数据保存失败',
            ]);
        }
    }
}
