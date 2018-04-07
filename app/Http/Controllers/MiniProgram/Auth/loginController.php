<?php

namespace App\Http\Controllers\MiniProgram\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request){
        //Get auth data from headers
        $authData = $this->getAuthData($request);
       if($authData  === null)
            return 'Data not complete';

        //Check auth data
        $authResult = $this->checkAuthData($authData);
        if($authResult === false)
            return 'Auth Failed';

        //generate 3rd key (skey)
        $skey = $this->generateSkey($authResult);

        //decrypt userinfo
        $decryptedUserInfo = $this->decryptUserInfo($authData, $authResult);

        //save data to db

        //send login success response to client with skey



        return 'openid = ' . $authResult->openid . '  session_key = ' . $authResult->session_key;
    }

    /**
     * Get login data from miniprogram (include code, iv and encryptedData)
     * @param {Object} Request Object
     * @return mixed if data complete,the return value is include code, iv and encryptedData. else return null
     */
    private function getAuthData(Request $request){
        //Get the code, iv and encryptedData from the header.
        $encryptedData = $request->header(config('constants.WX_HEADER_ENCRYPTED_DATA'));
        $iv = $request->header(config('constants.WX_HEADER_IV'));
        $code = $request->header(config('constants.WX_HEADER_CODE'));

        //Lack some data
        if(!$encryptedData or !$iv or !$code){
            return null;
        }

        return [
            'encryptedData' => $encryptedData,
            'iv' => $iv,
            'code' => $code,
        ];
    }

    /**
     * Send data to wechat Authorize Aerver.
     * @param {Array} auth data include appid + appsecret + code
     * @return mixed if auth seccuss the return value is json object include openid and session_key, else return false.
     */
    private function checkAuthData($authData){
        $appId = config('miniprogram-laravel-config.APP_ID');
        $appSecret = config('miniprogram-laravel-config.APP_SECRET');
        $host = config('miniprogram-laravel-config.WX_AUTH_INTERFACE_HOST');
        $path = config('miniprogram-laravel-config.WX_AUTH_INTERFACE_PATH');
        $method = "GET";
        $headers = array();
        $querys = 'appid=' . $appId . '&secret=' . $appSecret . '&js_code=' . $authData['code'] . '&grant_type=authorization_code';
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($curl);

        //separate the body
        $header = null;
        $body = null;
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }
        //string to stdclass's object
        $result = json_decode($body);

        //auth failed
        if(property_exists($result, 'errcode'))
            return false;
        else
            return $result;
    }

    /**
     * @param $authData
     * @param $authResult
     * @return string decrypt data
     */
    private function decryptUserInfo($authData, $authResult){
        //decrypt userinfo and save to db
        $decryptData = \openssl_decrypt(
            base64_decode($authData['encryptedData']),
            'AES-128-CBC',
            base64_decode($authResult->session_key),
            OPENSSL_RAW_DATA,
            base64_decode($authData['iv'])
        );
        return $decryptData;
    }

    /**
     * Create skey and return
     * @param $authResult
     * @return string skey
     */
    private function generateSkey($authResult){
        // generate 3rd key (skey)
        $skey = sha1($authResult->session_key . mt_rand());
        return $skey;
    }
}