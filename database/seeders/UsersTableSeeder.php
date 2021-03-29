<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $t = Carbon::now();
        DB::table('users')->insert([
            [
                'id' => 1,
                'email' => "alert+lukas@gemeindeonline.ch",
                'first_name' => "Lukas",
                'last_name' => "Andersson",
                'registered_at' => $t
            ],
            [
                'id' => 2,
                'email' => "alert+josy@gemeindeonline.ch",
                'first_name' => "Josy",
                'last_name' => "Lindberg",
                'registered_at' => $t
            ],
            [
                'id' => 3,
                'email' => "alert+ernst@gemeindeonline.ch",
                'first_name' => "Ernst",
                'last_name' => "Lindberg",
                'registered_at' => $t
            ],
            [
                'id' => 4,
                'email' => "alert+stefan@gemeindeonline.ch",
                'first_name' => "Stefan",
                'last_name' => "Meyer",
                'registered_at' => $t
            ]
        ]);
    }
}
