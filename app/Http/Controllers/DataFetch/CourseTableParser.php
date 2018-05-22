<?php

namespace App\Http\Controllers\DataFetch;

use QL\QueryList;

/**
 * 课程表HTML数据，解析类
 * Class CourseTableParser
 * @package App\Http\Controllers\DataFetch
 * @author Sao Guang
 */
class CourseTableParser{
    /**
     * 解析课程表数据（HTML -> array）
     * @param $strHtml
     * @return array
     * @author Sao Guang
     */
    public function parseData($strHtml){
        $coursesData = array();
        $data = QueryList::html($strHtml);
        $data->find('.kbcontent')->map(function ($item)use(&$coursesData){
            $itemHtml = $item->html();
            if($itemHtml == '&nbsp;'){
                array_push($coursesData, 'none');
            }else{
                //匹配出所有的课程信息。(当天)
                $str = '<br>'.$itemHtml;
                //$count = preg_match_all('/(?<=<br>)((?!<|>|-).)*(?=<br>)|(?<=>)((?!<|>).)*(?=<\/font>)/u', $str, $matches);
                $count = preg_match_all('/(?<=<br>)((?!<|>|-{2,}).)*(?=<br>)|(?<=>)((?!<|>).)*(?=<\/font>)/u', $str, $matches);
                //读入当天的课程
                $day = array();
                $courseData = array();
                for($i = 0; $i < $count; $i++){
                    array_push($courseData, str_replace(['★', '☆', '△', '■', '◣'], '', $matches[0][$i]));
                    if(!(($i + 1) % 4)){
                        array_push($day, $courseData);
                        $courseData = array();
                    }
                }
                //当天数据存入课程数据
                array_push($coursesData, $day);
            }
        });
        //获取学年和学期信息
        $str = $data->find('#xnxq01id')->html();

        preg_match_all('/(?<=selected>).*(?=<\/option>)/u', $str, $matches);

        $coursesData['year'] = substr($matches[0][0], 0, 4);
        $coursesData['term'] = substr($matches[0][0], 10, 1);
        return $coursesData;
    }
    /**
     * 解析周次
     * @param $weekth
     * @return array
     */
    public function parseWeekth($weekth){
        $weekths = array();
        //获取单周还是双周
        $oddWeek = false;
        $pluralWeek = false;
        if(strpos($weekth, '单周') !== false){
            $oddWeek = true;
        }
        if(strpos($weekth, '双周') !== false){
            $pluralWeek = true;
        }
        //读取全部显示出的数字
        preg_match_all('/\d+/', $weekth, $matches);
        foreach ($matches[0] as $match){
            if($oddWeek && !$this->isOddNum($match))
                continue;
            if($pluralWeek && $this->isOddNum($match))
                continue;
            $weekths[$match] = intval($match);
        }
        //读取1-19这样的时间起始和终止
        $count = preg_match_all('/\d+(?=-)/', $weekth, $matches);
        $startArr = $matches[0];
        preg_match_all('/(?<=-)\d+/', $weekth, $matches);
        $endArr = $matches[0];
        //获取中间夹着的所有数字
        for($i = 0; $i < $count; $i++){
            for($j = $startArr[$i] + 1; $j < $endArr[$i]; $j++){
                if($oddWeek && !$this->isOddNum($j))
                    continue;
                if($pluralWeek && $this->isOddNum($j))
                    continue;
                $weekths[$j] = $j;
            }
        }
        return $weekths;
    }

    /**
     * 是否为单数
     * @param $num
     * @return bool
     */
    private function isOddNum($num){
        return $num % 2 != 0;
    }
}