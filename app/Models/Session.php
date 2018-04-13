<?php

namespace App\Models;
//By Sao Guang
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
}