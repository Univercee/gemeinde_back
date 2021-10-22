<?php

namespace Database\Seeders\Test;

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
                'email' => "zahhar@gmail.com",
                'first_name' => "Zahhar",
                'last_name' => "Kirillov",
                'telegram_id' => "3234077",
                'telegram_username' => "zahhar",
                'avatar' => "https://t.me/i/userpic/320/hMo-wpWRBSy64Ig7ICZapzDwRazKcVKQERztQzGAksM.jpg",
                'registered_at' => $t
            ]
        ]);
    }
}
