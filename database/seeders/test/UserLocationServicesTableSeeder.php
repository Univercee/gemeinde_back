<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use DB;

class UserLocationServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_location_services')->insert([
            [
                'id' => 1,
                'user_location_id' => 1,
                'service_id' => 1,
                'channel' => 'Telegram'
            ],
            [
                'id' => 2,
                'user_location_id' => 1,
                'service_id' => 7,
                'channel' => 'Telegram'
            ]
        ]);
    }
}
