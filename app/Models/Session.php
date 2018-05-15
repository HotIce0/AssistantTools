<?php

namespace App\Models;
//By Sao Guang
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Session extends Model
{
    //指定表名
    protected $table = 't_session';

    protected $primaryKey = 'session_id';

    /**
     * @param $decryptedUserInfo
     * @param $skey
     * @param $sessionKey
     * @return bool
     */
    public static function storeUserInfo($decryptedUserInfo, $skey, $sessionKey){
        $userInfo = json_decode($decryptedUserInfo);
        $openId = $userInfo->openId;
        $unionId = $userInfo->unionId;

        //create new session
        $session = Session::where('open_id', '=', $openId)->first();
        if(empty($session)){
            $session = new Session();
            $session->open_id = $openId;
        }

        $session->union_id = $unionId;
        $session->skey = $skey;
        $session->wx_session_id = $sessionKey;
        $session->user_info = $decryptedUserInfo;
        if($session->save())
            return true;
        else
            return false;
    }

    /**
     * @param $sky
     * @return mixed if fail,return empty, else return ORM object
     */
    public static function findUserInfoBySKey($sky){
        return Session::where('skey', '=', $sky)->first();
    }

    /**
     * @param Request $request
     * @return array|bool
     */
    public static function checkLogin(Request $request){
        //skey missing
        $skey = $request->header(config('constants.WX_HEADER_SKEY'));
        if($skey === null)
            return [
                'code' => -1,
                'error' => 'skey missing',
            ];
        $session = self::findUserInfoBySKey($skey);

        if(empty($session)){
            //$skey invalied
            return [
                'code' => -1,
                'error' => 'skey invalied',
            ];
        }

        //check login expires
        if(self::checkLoginExpires($session))
            return [
                'code' => 0,
            ];
        else
            return [
                'code' => -1,
                'error' => 'login expired',
            ];
    }

    /**
     * @param $session
     * @return bool
     */
    private static function checkLoginExpires($session){
        $wxLoginExpires = config('miniprogram-laravel-config.WX_LOGIN_EXPIRES');
        $timeDifference = time() - strtotime($session->updated_at);

        if($timeDifference > $wxLoginExpires)
            return false;
        else
            return true;
    }

    /**
     * @param Request $request
     * @return string || null
     */
    public static function getSKeyFromRequest(Request $request){
        return $request->header(config('constants.WX_HEADER_SKEY'));
    }

    /**
     * 验证登陆状态，如果成功则返回携带数据库session对象
     * @param Request $request
     * @return array|bool 返回值中的code 为0 代表成功.-1代表失败。
     * @author Sao Guang
     */
    public static function checkLoginAndGetSession(Request $request){
        $res = Session::checkLogin($request);
        if($res['code'] == -1){
            //登陆验证失败
            return $res;
        }

        $skey = Session::getSKeyFromRequest($request);

        //获取Session记录
        $session = Session::findUserInfoBySKey($skey);

        $result['session'] = $session;
        $result['code'] = 0;

        return $result;
    }

    /**
     * 是否绑定了平台账号
     * @param $session_id
     * @return bool| $user 如果绑定了平台账号，返回平台账号信息，否则返回false
     * @author Sao Guang
     */
    public static function isBind($session_id){
        //查询用户绑定状态
        $user = User::where('session_id', '=', $session_id)
            ->first();
        if($user === null)
            return false;
        else
            return $user;
    }
}