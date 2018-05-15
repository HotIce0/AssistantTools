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

    /**
     * DataFetchController constructor.
     * @param $studentId
     * @param $cardId
     * @author Sao Guang
     */
    public function __construct($studentId, $cardId)
    {
        $this->studentId = $studentId;
        $this->cardId = $cardId;
    }

    /**
     * 获取个人课程表数据
     * @param null $year 学期开始的年 如2017
     * @param null $term 学期1或2
     * @param '' $weekNum 周次 默认全部周次
     * @return array|string 如果返回array则获取成功，如果返回string，则代表失败，string内容为错误提示信息。
     * @author Sao Guang
     */
    public function getPersonalCourseTableData($year = null, $term = null, $weekNum = ''){
        if(!$this->tryLogin()){
            return '登陆失败，可能是学院网站异常或者学号不存在';
        }

        //获取课表html
        $courseDataHtml = $this->getPersonalCourseTable($year, $term, $weekNum);
        if($courseDataHtml == false)
            return '课程表获取失败';

        //解析课程表数据到数组
        $parser = new CourseTableParser();
        $courseDataParsed = $parser->parseData($courseDataHtml);
        return $courseDataParsed;
    }


    /**
     * 重置账号的密码
     * @return bool|string
     * @author Sao Guang
     */
    private function ResetPersonalPassword(){
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
            return '首次登陆失败';
        }

        //修改密码
        if(!$this->alterPassword()){
            return '修改密码失败';
        }

        return true;
    }

    /**
     * 尝试登陆到教务管理系统
     * @return bool
     * @author Sao Guang
     */
    private function tryLogin(){
        if(!$this->getSessionId()){
            return false;
        }
        if(!$this->login(DataFetchController::DEFAULT_PASSWROD)){
            //登陆失败

            //重置密码
            if(!$this->ResetPersonalPassword()){
                //重置失败
                return false;
            }

            //再次尝试登陆
            if(!$this->login(DataFetchController::DEFAULT_PASSWROD))
                return false;
            else
                return true;
        }
        return true;
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
     * 重置用户密码 步骤请求
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
     * 登陆 步骤请求
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
     * 修改密码 步骤请求
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
     * 获取初始课程表网页数据 步骤请求
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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);//释放curl
        if($httpCode === 200)
            if(!empty($res))
                return $res;
            else
                return false;
        else
            return false;
    }

    /**
     * 获取个人课程表 步骤请求
     * @param $startYear 学年开始的年份如2017
     * @param $term 学期1或2
     * @param string $weekNum 周次如1，2....默认值为空，代表所有周次
     * @return bool|mixed
     * @author Sao Guang
     */
    private function getPersonalCourseTable($startYear = null, $term = null, $weekNum){
        $header = [
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $cookie = 'JSESSIONID=' . $this->jsessionid . ';';
        if($startYear != null && $term != null)
            $postFields = 'zc='.$weekNum.'&xnxq01id='.$startYear.'-'.($startYear + 1).'-'.$term;
        else
            $postFields = 'zc='.$weekNum;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://bkjw.hnist.cn/jsxsd/xskb/xskb_list.do');
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
            if(!empty($res))
                return $res;
            else
                return false;
        }else{
            return false;
        }
    }
}