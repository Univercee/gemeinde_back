<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            [
                'zipcode' => "3216",
                'name_en' => "Agriswil",
                'name_de' => "",
                'region' => "FR",
                'lat' => 47.5452177,
                'lng' => 9.2759004
            ],
            [
                'zipcode' => "1730",
                'name_en' => "Ecuvillens",
                'name_de' => "",
                'region' => "FR",
                'lat' => 46.7500178,
                'lng' => 7.0745792
            ],
            [
                'zipcode' => "1483",
                'name_en' => "Vesin",
                'name_de' => "",
                'region' => "FR",
                'lat' => 47.5452177,
                'lng' => 9.2759004
            ],
            [
                'zipcode' => "1233",
                'name_en' => "Bernex",
                'name_de' => "",
                'region' => "GE",
                'lat' => 46.3566238,
                'lng' => 6.6739585
            ],
            [
                'zipcode' => "1219",
                'name_en' => "Chatelaine",
                'name_de' => "",
                'region' => "GE",
                'lat' => 46.2116862,
                'lng' => 6.1011794
            ]
        ]);
    }
}
