<?php

namespace App\Http\Controllers\DataFetch;

use function GuzzleHttp\Psr7\str;
use QL\QueryList;

class SchoolStartDate{
    /**
     * 解析学年学期列表
     * @param $HTML
     * @return array|string
     */
    public function parseYearTerms($HTML){
        $arrayYearTerms = array();
        $data = QueryList::html($HTML);
        $str = $data->find('#xnxq01id')->html();
        if(preg_match_all('/(?<=>)[0-9]{4,4}-[0-9]{4,4}-[1-2](?=<)/', $str, $matches) === false){
            return '匹配失败！可能是学院网页内容变更';
        }
        $arrayYearTerms = $matches[0];
        return $arrayYearTerms;
    }

    /**
     * 获取开学第一天
     * @param $HTML
     * @return mixed
     */
    public function parseYearTermStart($HTML){
        $startDate = '';
        $data = QueryList::html($HTML);
        $titles = $data->find('#kbtable')->find('td')->attrs('title');
        foreach ($titles as $title){
            if($title !== null){
                $strs = explode('-', str_replace(['年','月'], '-', $title));
                for($i = 0; $i < 3; $i++){
                    $startDate .= intval($strs[$i]);
                    if($i < 2){
                        $startDate .= '-';
                    }
                }
                return $startDate;
            }
        }
    }
}