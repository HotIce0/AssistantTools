<?php

namespace App\Http\Controllers\DataFetch;

class CourseTableParser{
    public function parseData($strHtml){
        $html = new \simple_html_dom();
        //加载网页数据
        $html->load($strHtml);

        $divs = $html->find('.Nsb_layout_r');
        dd($divs);
    }
}