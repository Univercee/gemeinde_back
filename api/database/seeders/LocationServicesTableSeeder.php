<?php

namespace Database\Seeders;

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
        $locationsIDs = DB::table('locations')->pluck('id');
        $servicesIDs= DB::table('services')->pluck('id');
        $faker = \Faker\Factory::create();
        foreach(range(0,5) as $i){     
            DB::table('location_services')->insert([
                'location_id' => $faker->randomElement($locationsIDs),
                'service_id' => $faker->randomElement($servicesIDs),
            ]);
        }
    }
}
