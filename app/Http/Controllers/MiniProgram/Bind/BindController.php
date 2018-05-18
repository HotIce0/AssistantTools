<?php

namespace App\Http\Controllers\MiniProgram\Bind;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataFetch\AuthNameCardnoJobID;
use App\Http\Controllers\DataFetch\DataFetchController;
use App\Http\Controllers\DataFetch\UserNameCardno;
use App\Models\Session;
use App\Models\StudentInfo;
use App\Models\TeacherInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 用户绑定控制器
 * Class BindController
 * @package App\Http\Controllers\MiniProgram\Bind
 * @author Sao Guang
 */
class BindController extends Controller{
    /**
     * 获取绑定状态的API
     * @param Request $request
     * @return array|bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBindStatus(Request $request){
        //验证登陆状态
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }
        //登陆验证通过
        $skey = Session::getSKeyFromRequest($request);

        //获取Session记录
        $session = Session::findUserInfoBySKey($skey);

        //查询用户绑定状态
        if(Session::isBind($session->session_id)){
            return response([
                'code' => 0,
                'bindstatus' => true,
            ]);
        }else{
            return response([
                'code' => 0,
                'bindstatus' => false,
            ]);
        }
    }

    /**
     * 绑定平台账号
     * @param Request $request
     * @return array
     */
    public function bind(Request $request){
        //验证登陆状态
        $res = Session::checkLoginAndGetSession($request);
        if(!apiIsSuccess($res)){
            return $res;
        }

        //登陆验证通过
        $skey = Session::getSKeyFromRequest($request);

        //获取Session记录
        $session = Session::findUserInfoBySKey($skey);

        //验证规则
        $rules = array(
            'name' => 'required|string',
            'jobId' => 'required|integer',
            'idCard' => [
                'required',
                'regex:/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/'
            ],
            'classID' => 'integer|exists:t_class,class_id',
            'collegeID' => 'integer|exists:t_college,college_id'
        );
        //错误消息
        $message = array(
            'required'=>':attribute 必须填写',
            'string' => ':attribute 必须是字符串',
            'idCard.regex' => ':attribute 格式不正确',
            'integer' => ':attribute 必须由整数组成',
            'classID.exists' => ':attribute 该班级不存在',
            'collegeID.exists' => ':attribute 该学院不存在',
        );
        //字段意义
        $meaning = array(
            'name' => '姓名',
            'jobId' => '学号或工号',
            'idCard' => '身份证号码',
            'classID' => '班级ID',
            'collegeID' => '学院ID',
        );
        //表单验证
        $validator = Validator::make($request->all(), $rules, $message, $meaning);
        if($validator->fails()){
            return apiFailResponse($validator->errors());
        }

        //验证姓名和身份证与学号的一致性
        $auth = new UserNameCardno();
        $info = $auth->getInfo($request->name, $request->idCard);
        if($info === false){
            $errors = new \stdClass();
            $errors->message = '认证信息无效，请确认认证信息正确';
            return apiFailResponse($errors);
        }

        //判断学号与获取到的学生信息是否一致
        if($info['jobID'] != $request->jobId){
            $errors = new \stdClass();
            $errors->message = '认证信息无效，请确认认证信息正确';
            return apiFailResponse($errors);
        }
        //确定学生，上传的是班级ID，教师是学院ID
        if($info['isStudent']){
            if($request->classID === null){
                $errors = new \stdClass();
                $errors->message = '缺少班级ID信息';
                return apiFailResponse($errors);
            }
        }else{
            if($request->collegeID === null){
                $errors = new \stdClass();
                $errors->message = '缺少学院ID信息';
                return apiFailResponse($errors);
            }
        }

        //认证通过，创建用户信息，创建用户，并进行绑定

        //创建用户信息
        //创建新的用户信息(根据个人信息判断是学生还是教师)
        $userInfo = null;
        if($info['isStudent']){
            //创建学生信息
            $userInfo = StudentInfo::where('user_job_id', '=', $info['jobID'])->first();
            if($userInfo === null)
                $userInfo = new StudentInfo();
            $userInfo->name = $info['name'];
            $userInfo->user_job_id = $info['jobID'];
            $userInfo->class_id = $request->classID;
            $userInfo->id_card_no = $info['cardno'];
            $userInfo->sex = $info['isMan'] ? '男' : '女';
            $userInfo->email = $info['email'];

            if(!$userInfo->save()){
                $errors = new \stdClass();
                $errors->message = '服务器数据库存储异常:用户信息创建失败!';
                return apiFailResponse($errors);
            }
        }else{
            //创建教师信息
            $userInfo = TeacherInfo::where('user_job_id', '=', $info['jobID'])->first();
            if($userInfo === null)
                $userInfo = new TeacherInfo();
            $userInfo->name = $info['name'];
            $userInfo->user_job_id = $info['jobID'];
            $userInfo->college_id = $request->collegeID;
            $userInfo->id_card_no = $info['cardno'];
            $userInfo->sex = $info['isMan'] ? '男' : '女';
            $userInfo->email = $info['email'];

            if(!$userInfo->save()){
                $errors = new \stdClass();
                $errors->message = '服务器数据库存储异常:用户信息创建失败!';
                return apiFailResponse($errors);
            }
        }

        //创建用户。并绑定微信
        $user = User::findUserByJobId($request->jobId);
        if($user === null){
            //创建新的用户
            $user = new User();
        }

        $user->user_job_id = $info['jobID'];
        $user->password = bcrypt(substr($info['cardno'], 12, 6));//默认密码身份证后六位
        $user->role_id = 1;
        $user->session_id = $session->session_id;//绑定
        $user->user_type = $info['isStudent'] ? 0 : 1;//0为学生，1为教师
        $user->user_info_id = $info['isStudent'] ? $userInfo->student_info_id : $userInfo->teacher_info_id;

        if($user->save()){
            $data = new \stdClass();
            $data->message = '平台账号绑定成功';
            return apiSuccessResponse($data);
        }else{
            $errors = new \stdClass();
            $errors->message = '服务器数据库存储异常:用户创建失败!';
            return apiFailResponse($errors);
        }
    }
}
