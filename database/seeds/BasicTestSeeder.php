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
        DB::table('users')->insert([
            'user_job_id' => '14162400891',
            'password' => bcrypt('18577X'),
            'role_id' => 1,
            'session_id' => null,
            'user_type' => 0,
            'user_info_id' => 1,
        ]);
    }
}
