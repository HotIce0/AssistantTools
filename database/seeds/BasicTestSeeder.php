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
        //插入测试用户
//        DB::table('users')->insert([
//            'user_job_id' => '14162400891',
//            'password' => bcrypt('18577X'),
//            'role_id' => 1,
//            'session_id' => null,
//            'user_type' => 0,
//            'user_info_id' => 1,
//        ]);

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
            'item_content' => '2017-2',
            'sort_id' => 0,
        ]);

        //插入角色
        DB::table('t_role')->insert([
            'role_name' => "平台会员",
            'role_permission' => '[]',
        ]);
    }
}
