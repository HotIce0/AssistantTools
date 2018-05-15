<?php

namespace App;

use App\Models\Role;
use App\Models\StudentInfo;
use App\Models\TeacherInfo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 * @author SaoGuang
 */
class User extends Authenticatable
{
    use Notifiable;

    //软删除
    use SoftDeletes;
    //指定软删除标记字段
    protected $dates = ['delete_at'];
    //指定表名
    protected $table = 'users';
    //指定主键
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 判断用户是否有该权限（权限编号）
     * @param string $permisson
     * @return bool
     * @author Sao Guang
     */
    public function hasPermission($permisson)
    {
        //获取对应角色
        $role = Role::find($this->role_id);
        //判断角色是否有该权限
        return $role->hasPermission($permisson);
    }

    /**
     * 获取用户信息模型对象
     * @return null
     * @author Sao Guang
     */
    public function getUserInfo()
    {
        if($this->user_type == config('constants.USER_TYPE_STUDENT'))
        {
            return StudentInfo::find($this->user_info_id);
        }elseif ($this->user_type == config('constants.USER_TYPE_TEACHER'))
        {
            return TeacherInfo::find($this->user_info_id);
        }
        return null;
    }

    /**
     * @param $jobId
     * @return null
     * @author Sao Guang
     */
    public static function findUserByJobId($jobId){
        $user = User::where('user_job_id', '=', $jobId)->first();
        if(empty($user))
            return null;
        else
            return $user;
    }

    /**
     * 是否是学生
     * @return bool
     * @author Sao Guang
     */
    public function isStudent(){
        if($this->user_type == 0)
            return true;
        else
            return false;
    }
}
