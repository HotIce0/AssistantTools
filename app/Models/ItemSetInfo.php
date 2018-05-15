<?php

namespace App\Models;
//By Sao Guang
use Illuminate\Database\Eloquent\Model;

class ItemSetInfo extends Model
{
    protected $table = 't_item_set_info';

    protected $primaryKey = 'item_id';

    /**
     * 设置年和学期
     * @param $year
     * @param $term
     * @return bool
     */
    public static function setYearTerm($year, $term){
        $res = ItemSetInfo::where('item_no', '=', 5)->first();
        $res->item_content = $year . '-' . $term;
        if($res->save())
            return true;
        else
            return false;
    }

    /*
     * 获取年、学期
     */
    private static function getYearTerm(){
        $res = ItemSetInfo::where('item_no', '=', 5)->first();
        return explode('-', $res->item_content);
    }

    /**
     * 获取现在的学期
     * @return mixed
     */
    public static function getYearNow(){
        $strArray = ItemSetInfo::getYearTerm();
        return $strArray[0];
    }

    /**
     * 获取现在的学期
     * @return mixed
     */
    public static function getTermNow(){
        $strArray = ItemSetInfo::getYearTerm();
        return $strArray[1];
    }
}