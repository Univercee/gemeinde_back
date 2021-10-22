<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use DB;
class LocationServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('location_services')->insert([
            [
                'location_id' => 183, //8603 Schwerzenbach
                'service_id' => 1,
            ],
            [
                'location_id' => 183, //8603 Schwerzenbach
                'service_id' => 7,
            ],
            [
                'location_id' => 2,
                'service_id' => 1,
            ],
            [
                'location_id' => 2,
                'service_id' => 2,
            ],
            [
                'location_id' => 3,
                'service_id' => 1,
            ],
            [
                'location_id' => 5,
                'service_id' => 1,
            ],
            [
                'location_id' => 5,
                'service_id' => 2,
            ],
            [
                'location_id' => 5,
                'service_id' => 3,
            ],
        ]);
    }
}
