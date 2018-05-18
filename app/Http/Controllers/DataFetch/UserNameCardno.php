<?php

namespace App\Http\Controllers\DataFetch;

use QL\QueryList;

/**
 * 用户认证信息提供类（通过学院网站爬取）
 * Class UserNameCardno
 * @package App\Http\Controllers\DataFetch
 * @author Sao Guang
 */
class UserNameCardno{
    /**
     * 通过学号身份证，获取个人信息，其中包含，学号，电子邮箱
     * @param $name
     * @param $cardno
     * @return array|bool 如果返回false，代表姓名和身份证不匹配
     */
    public function getInfo($name, $cardno){
        //通过学院账号查询网站查询账号信息
        $userData = $this->doAuthRequest($name, $cardno);
        return $userData;
    }

    /**
     * 验证请求
     * @param $name
     * @param $cardno
     * @return array|bool
     */
    private function doAuthRequest($name, $cardno){
        $res = false;
        $bools = [true, false];
        //验证 男，女，学生，教师所有组合
        for($i = 0; $res === false and $i < 2; $i++){
            for($j = 0; $res === false and $j < 2; $j++){
                $res = $this->getAuthData($name, $cardno, $bools[$i], $bools[$j]);
                if($res !== false){
                    $res['isMan'] = $bools[$i];
                    $res['isStudent'] = $bools[$j];
                }
            }
        }
        return $res;
    }

    /**
     * getAuthData
     * @param $name
     * @param $cardno
     * @param $isMan
     * @param $isStudent
     * @return array|bool
     */
    private function getAuthData($name, $cardno, $isMan, $isStudent){
        $header = [
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $postFields = 'domain.name='.urlencode($name).'&domain.cardId='.$cardno.'&genders='.urlencode($isMan?'男':'女').'&roleName='.($isStudent?'0':'1');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://uia.hnist.cn/uiauser/patch/selfServiceQuery.action');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//设置不输出返回数据到页面。
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);//释放curl
        if($httpCode == 200){
            //判断是否有数据
            if(preg_match_all('/暂无数据/u', $res)){
                return false;
            }else{
                return $this->parseHtml($res);
            }
        }else
            return false;
    }

    /**
     * 解析html数据，取到个人账号信息
     * @param $html
     * @return array
     */
    private function parseHtml($html){
        $userData = array();
        $index = 0;
        $userDataName = ['ID', 'name', 'jobID', 'cardno', 'phoneNumber', 'email'];
        $data = QueryList::html($html);
        $data->find('td')->map(function ($item)use(&$userData, &$userDataName, &$index){
            $userData[$userDataName[$index]] = $item->text();
            $index++;
        });
        return $userData;
    }
}