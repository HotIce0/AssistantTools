<?php

namespace App\Models;
//By Sao Guang
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\Cloner\Data;

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
    /**
     * 获取学年学期开始
     * @return array
     */
    public static function getStartYearTerm(){
        $startItem = ItemSetInfo::where('item_no', '=', 6)->first();
        return explode('-', $startItem->item_content);
    }

    /**
     * 获取学年学期终止
     * @return array
     */
    public static function getEndYearTerm(){
        $endItem = ItemSetInfo::where('item_no', '=', 7)->first();
        return explode('-', $endItem->item_content);
    }

    /**
     * 获取对应学年，学期的，开学日期
     * @param $year
     * @param $term
     * @return array
     */
    public static function getStartDataByYearTerm($year, $term){
        $startData = ItemSetInfo::where('item_no', '=', '8')
            ->where('item_content_id','=', $year.'-'.$term)
            ->first();
        return explode('-', $startData->item_content);
    }
}