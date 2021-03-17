<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class SessionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            [
                'id' => 1,
                'user_id' => "1",
                'session_key' => "26f26a10d759475837bfb3cfb9467ec611725b8001f96778904a597c038f07b4",
                'key_at' => "2021-03-16 14:31:00",
                'key_until' => "2021-07-17 14:31:00",
                'ip_address' => null,
                'browser' => null,
            ]
        ]);
    }
}
