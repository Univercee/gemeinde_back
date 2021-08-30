<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Real production data
        $this->call(Production\LocationsTableSeeder::class);
        $this->call(Production\ServicesTableSeeder::class);
        $this->call(Production\LocationServicesTableSeeder::class);
        $this->call(Production\GarbageCalendars2021\Schwerzenbach8603::class);

        if (App::environment(['local','dev','test'])) {
            //Demo and test data
            $this->call(Test\UsersTableSeeder::class);
            $this->call(Test\UserLocationsTableSeeder::class);
        }

    }
}