<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use DB;

class UserLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_locations')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'location_id' => 183,
                'title' => 'Home',
                'street_name' => 'Bahnhofstrasse',
                'street_number' => '37'
            ]
        ]);
    }
}
