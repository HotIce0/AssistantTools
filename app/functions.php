<?php


if (! function_exists('apiFailResponse')) {
    /**
     * 发送API失败响应
     * @param $error
     * @return string
     * @author SaoGuang
     */
    function apiFailResponse($error){
        return json_encode([
            'code' => -1,
            'error' => $error,
        ]);
    }
}

if (! function_exists('apiSuccessResponse')) {
    /**
     * 发送API成功响应
     * @param $data
     * @return string
     * @author
     */
    function apiSuccessResponse($data){
        return json_encode([
            'code' => 0,
            'data' => $data,
        ]);
    }
}
