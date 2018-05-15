<?php

namespace App\Http\Controllers\DataFetch;

use QL\QueryList;

/**
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
        $courseTableRule = array(
            'kbcontent' => ['.kbcontent', 'text'],
            'teacher' => ['[.kbcontent]']
        );
        $rule = array(
            'zc' => array('#zc', 'text'),
            'xnxq01id' => array('#xnxq01id', 'text'),
        );

        $coursesData = array();

        $data = QueryList::html($strHtml);
        $data->find('.kbcontent')->map(function ($item)use(&$coursesData){
            $itemHtml = $item->html();
            if($itemHtml == '&nbsp;'){
                array_push($coursesData, 'none');
            }else{
                //匹配出所有的课程信息。(当天)
                $str = '<br>'.$itemHtml;
                $count = preg_match_all('/(?<=<br>)((?!<|>|-).)*(?=<br>)|(?<=>)((?!<|>).)*(?=<\/font>)/u', $str, $matches);
                //读入当天的课程
                $day = array();
                $courseData = array();
                for($i = 0; $i < $count; $i++){
                    array_push($courseData, str_replace(['△', '■', '◣'], '', $matches[0][$i]));
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
        //dd($str);
        preg_match_all('/(?<=selected>).*(?=<\/option>)/u', $str, $matches);


        $coursesData['year'] = substr($matches[0][0], 0, 4);
        $coursesData['term'] = substr($matches[0][0], 10, 1);
        return $coursesData;
    }
}