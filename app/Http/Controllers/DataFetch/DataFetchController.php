<?php

namespace App\Http\Controllers\DataFetch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataFetchController extends Controller{
    //修改密码后的默认密码
    const DEFAULT_PASSWROD = 'qq51747708';
    //学生证号码
    private $studentId;
    //身份证号码
    private $cardId;
    //Session Id
    private $jsessionid;

//    public function __construct($studentId, $cardId)
//    {
//        $this->studentId = $studentId;
//        $this->cardId = $cardId;
//    }

    public function index(){
        $this->studentId = '14162400891';
        $this->cardId = '43092119971118577X';
        //获取Session ID
        if(!$this->getSessionId()){
            return '获取session_id失败';
        }
        //重置用户密码
        if(!$this->resetPassword()){
            return '置用户密码失败';
        }

        //首次登陆(密码为学号)
        if(!$this->login($this->studentId)){
            return '登陆失败1';
        }

        //修改密码
        if(!$this->alterPassword()){
            return '修改密码失败';
        }

        //再次登陆(密码为默认密码)
        if(!$this->login(DataFetchController::DEFAULT_PASSWROD)){
            return '登陆失败2';
        }

        //获取课程表数据
        $courseData = $this->getCourseTable();

        $parser = new CourseTableParser();
        $parser->parseData($courseData);

        return 'ok';
    }

    /**
     * 获取session id
     * @return bool
     * @author Sao Guang
     */
    private function getSessionId(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/index.jsp');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HEADER, true);//获取头部
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header = substr($res, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        curl_close($ch);//释放curl
        if($httpCode == 200){
            if(preg_match('/(?<=JSESSIONID=).*?(?=;)/', $header, $matchRes)) {//获取session id
                $this->jsessionid = $matchRes[0];
                return true;
            }else{
                false;
            }
        }else{
            return false;
        }
    }

    /**
     * 重置用户密码
     * @return bool
     * @author Sao Guang
     */
    private function resetPassword(){
        $header = [
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $cookie = 'JSESSIONID=' . $this->jsessionid . ';';
        $postFields = 'account=' . $this->studentId . '&sfzjh=' . $this->cardId;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/user/resetPasswd');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);//释放curl
        if($httpCode == 200){
            if(preg_match('/密码已重置和帐号一致/', $res))//判断密码是否重置成功
                return true;
            else
                false;
        }else
            return false;
    }

    /**
     * 登陆
     * @param $password
     * @return bool
     * @author Sao Guang
     */
    private function login($password){
        $header = [
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $cookie = 'JSESSIONID=' . $this->jsessionid . ';';
        $postFields = 'encoded=' . urlencode(base64_encode($this->studentId) . '%%%' . base64_encode($password));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/xk/LoginToXk');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);//释放curl
        if($httpCode == 302)//返回302时代表登陆成功。
            return true;
        else
            return false;
    }

    /**
     * 修改密码
     * @return bool
     * @author Sao Guang
     */
    private function alterPassword(){
        $header = [
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $cookie = 'JSESSIONID=' . $this->jsessionid . ';';
        $postFields = 'id=&oldpassword='.$this->studentId.'&password1='.DataFetchController::DEFAULT_PASSWROD.'&password2='.DataFetchController::DEFAULT_PASSWROD.'&button1=%E4%BF%9D+%E5%AD%98&upt=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/grsz/grsz_xgmm_beg.do');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);//释放curl
        if($httpCode == 200){
            if(preg_match('/密码修改成功,请重新登录/', $res))//判断密码是否修改成功
                return true;
            else
                false;
        }else
            return false;
    }

    /**
     * 获取初始课程表网页数据
     * @return bool|mixed
     * @author Sao Guang
     */
    private function getCourseTable(){
        $cookie = 'JSESSIONID=' . $this->jsessionid . ';';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/xskb/xskb_list.do');//获取课程表数据URL
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        $res = curl_exec($ch);
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200)
            return $res;
        else
            return false;
    }
}