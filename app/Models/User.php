<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class User extends Model{
    //指定表名
    protected $table = 'users';

    protected $primaryKey = 'id';
}