<?php

namespace App\Http\Controllers\DataFetch;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DataFetchController extends Controller{
    const LOGIN_URL = 'http://bkjw.hnist.cn/jsxsd/xk/LoginToXk';
    const PERSONAL_COURSE_REQUEST_URL = 'http://bkjw.hnist.cn/jsxsd/xskb/xskb_list.do';

    private $cookie;
    public function __construct()
    {
        $this->cookie = array();
    }

    public function index(Request $request){
        //send login request to server
        $res = $this->authRequest('14162400891', 'Zengguang577X');
        //get j session id
        $arr = $this->getJSESSIONID($res);
        if($arr !== null){
            $this->cookie['JSESSIONID'] = $arr['JSESSIONID'];
        }

        //send post to get course table pages data
        return serialize($this->getPersonalCourseTableData());

    }

    private function getPersonalCourseTableData(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, DataFetchController::PERSONAL_COURSE_REQUEST_URL);
        curl_setopt($curl, CURLOPT_COOKIE, 'JSESSIONID=' . $this->cookie['JSESSIONID']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        $res = curl_exec($curl);
        if(curl_getinfo($curl, CURLINFO_HTTP_CODE) === 200)
            return $res;
        else
            return false;
    }

    /**
     * 登陆验证请求发送
     * @param $username
     * @param $password
     * @return bool|mixed
     */
    private function authRequest($username, $password){
        $encodedUsername = base64_encode($username);
        $encodedPassword = base64_encode($password);
        $encoded = $encodedUsername . '%%%' . $encodedPassword;

        $method = 'POST';
        $headers = array();
        $bodys = 'encoded=' . urlencode($encoded);
        $url = DataFetchController::LOGIN_URL;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, true);       //设置之后才能获取到头部

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        //不直接输出到页面
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($curl);

        if(curl_getinfo($curl, CURLINFO_HTTP_CODE) == 302){
            curl_close($curl);
            return $res;
            return true;
        }else{
            curl_close($curl);
            return false;
        }
    }

    /**
     * @param $headers
     * @return array|null if succcess the return value is array, that include the cookie's key and value.
     *                      else return null
     */
    private function getJSESSIONID($headers){
        $headerArr = explode("\r\n", $headers);
        //find set cookie
        foreach ($headerArr as $item){
            if(strpos($item, 'Set-Cookie') !== false){
                $startKey = strpos($item, ':');
                $endKey = strpos($item, '=');
                $startValue =  strpos($item, '=');
                $endValue = strpos($item, ';');
                $key = substr($item, $startKey + 2, $endKey - $startKey - 2);
                $value = substr($item, $startValue + 1, $endValue - $startValue - 1);
                //dd($item . ' ' . $key . ' ' . $value);
                return array(
                    $key => $value,
                );
            }
        }
        return null;
    }

    public function getIndex(Request $request){
        $url = 'http://52php.cnblogs.com';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $header = substr($res, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        return $header;
        return ;

        curl_close($ch);
    }
}
