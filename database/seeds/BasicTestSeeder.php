<?php

use Illuminate\Database\Seeder;

class BasicTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //课程类型
        DB::table('t_item_set_info')->insert([
            'item_no' => 1,
            'item_content_id' => '1',
            'item_content' => '正式课程',
            'sort_id' => 0,
        ]);
        DB::table('t_item_set_info')->insert([
            'item_no' => 1,
            'item_content_id' => '2',
            'item_content' => '早自习',
            'sort_id' => 0,
        ]);
        DB::table('t_item_set_info')->insert([
            'item_no' => 1,
            'item_content_id' => '3',
            'item_content' => '晚自习',
            'sort_id' => 0,
        ]);

        //插入学期选项
        DB::table('t_item_set_info')->insert([
            'item_no' => 4,
            'item_content_id' => '1',
            'item_content' => '上学期',
            'sort_id' => 0,
        ]);
        DB::table('t_item_set_info')->insert([
            'item_no' => 4,
            'item_content_id' => '2',
            'item_content' => '下学期',
            'sort_id' => 0,
        ]);

        //插入当前学年学期
        DB::table('t_item_set_info')->insert([
            'item_no' => 5,
            'item_content_id' => '1',
            'item_content' => '',
            'sort_id' => 0,
        ]);

        //插入角色
        DB::table('t_role')->insert([
            'role_id' => 1,
            'role_name' => "平台会员",
            'role_permission' => '[1]',
        ]);
        DB::table('t_role')->insert([
            'role_id' => 2,
            'role_name' => "系统管理员",
            'role_permission' => '[2,9]',
        ]);
        DB::table('t_role')->insert([
            'role_id' => 3,
            'role_name' => "班级管理员",
            'role_permission' => '[1,3,4,5,6,7,8,10,11,12]',
        ]);
        DB::table('t_role')->insert([
            'role_id' => 4,
            'role_name' => "学院管理员",
            'role_permission' => '[]',
        ]);
        DB::table('t_role')->insert([
            'role_id' => 5,
            'role_name' => "什么都能干的骚骚皮皮",
            'role_permission' => '[1,2,3,4,5,6,7,8,9,10,11,12]',
        ]);
    }
}
