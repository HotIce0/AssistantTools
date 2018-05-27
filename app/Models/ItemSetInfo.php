<?php

namespace App\Models;
//By Sao Guang
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\Cloner\Data;

class ItemSetInfo extends Model
{
    protected $table = 't_item_set_info';

    protected $primaryKey = 'item_id';

    /**
     * 设置现在的年和学期
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
    public static function getYearTerm(){
        $res = ItemSetInfo::where('item_no', '=', 5)->first();
        if($res->item_content == '')
            return false;
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
     * 设置学年学期开始
     * @param $year
     * @param $term
     * @return bool
     */
    public static function setStartYearTerm($yearTerm){
        $item = ItemSetInfo::where('item_no', '=', 6)->first();
        if($item === null){
            $item = new ItemSetInfo();
            $item->item_no = 6;
            $item->item_content_id = 1;
            $item->sort_id = 0;
        }
        $item->item_content = $yearTerm;
        return $item->save();
    }
    /**
     * 设置学年学期结束
     * @param $year
     * @param $term
     * @return bool
     */
    public static function setEndYearTerm($yearTerm){
        $item = ItemSetInfo::where('item_no', '=', 7)->first();
        if($item === null){
            $item = new ItemSetInfo();
            $item->item_no = 7;
            $item->item_content_id = 1;
            $item->sort_id = 0;
        }
        $item->item_content = $yearTerm;
        return $item->save();
    }

    /**
     * 获取对应学年，学期的，开学日期
     * @param $year
     * @param $term
     * @return array|bool
     */
    public static function getStartDateByYearTerm($year, $term){
        $startDate = ItemSetInfo::where('item_no', '=', '8')
            ->where('item_content_id','=', $year.'-'.$term)
            ->first();
        if($startDate === null)
            return false;
        return explode('-', $startDate->item_content);
    }

    /**
     * 通过学年和学期，计算出YMD时间的周次和星期（前提：必须开学时间在数据库内存在的学年学期）
     * @param $year
     * @param $term
     * @param $YMD
     * @return array|bool
     */
    public static function yearTermYMDToWeekthWeek($year, $term, $YMD){
        //年学期，获取开学的日期
        $arrayStartDate = ItemSetInfo::getStartDateByYearTerm($year, $term);
        if($arrayStartDate === false)
            return false;
        //时间对象
        $startDate = date_create_from_format("Y-n-j",intval($arrayStartDate[0]).'-'.intval($arrayStartDate[1]).'-'.intval($arrayStartDate[2]));
        $nowdate=date_create_from_format("Y-n-j",intval($YMD[0]).'-'.intval($YMD[1]).'-'.intval($YMD[2]));
        //计算YMD时间与开学时间之差
        $diff=date_diff($startDate,$nowdate);
        $daysDiff = $diff->days + 1;
        $weekth = intval($daysDiff / 7);
        $week = intval($daysDiff % 7);
        if(intval($daysDiff % 7) > 0)
            $weekth++;
        $weekthWeek = [
            (string)$weekth,
            $week == 0 ? (string)7 : (string)$week,
        ];
        return $weekthWeek;
    }

    /**
     * 删除所有学年学期，开学第一天日期数据
     */
    public static function deleteAllYearTermStartDate(){
        ItemSetInfo::where('item_no', '=', 8)//8为学年学期，开学第一天日期数据
            ->delete();
    }

    /**
     * 添加学年学期开学日期记录
     * @param $yearTerm
     * @param $startDay
     * @return bool
     */
    public static function addYearTermStartDate($yearTerm, $startDay){
        $item = new ItemSetInfo();
        $item->item_no = '8';
        $item->item_content_id = $yearTerm;
        $item->item_content = $startDay;
        $item->sort_id = 0;
        return $item->save();
    }
}