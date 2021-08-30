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
                'location_id' => 1,
                'title' => 'Summer house',
                'street_name' => 'Street',
                'street_number' => '1'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'location_id' => 2,
                'title' => 'Secret lab',
                'street_name' => "Won't",
                'street_number' => 'Say'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'location_id' => 3,
                'title' => 'Castle',
                'street_name' => 'Distant lands',
                'street_number' => '5'
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'location_id' => 4,
                'title' => 'Cat house',
                'street_name' => 'Meow meow meow',
                'street_number' => 'Meow meow'
            ]
        ]);
    }
}
