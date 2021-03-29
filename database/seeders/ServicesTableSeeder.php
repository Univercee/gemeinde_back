<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            [
                'id' => 1,
                'name_en' => "Garbage calendar",
                'name_de' => "",
            ],
            [
                'id' => 2,
                'name_en' => "News from local authority",
                'name_de' => "",
            ],
            [
                'id' => 3,
                'name_en' => "Weather warnings",
                'name_de' => "",
            ],
            [
                'id' => 4,
                'name_en' => "Neighbor alerts",
                'name_de' => "",
            ],
            [
                'id' => 5,
                'name_en' => "Local events",
                'name_de' => "",
            ],
            [
                'id' => 6,
                'name_en' => "Federal and Cantonal events",
                'name_de' => "",
            ],
            [
                'id' => 7,
                'name_en' => "Local news (unoffical)",
                'name_de' => "",
            ],
            [
                'id' => 8,
                'name_en' => "GGA Maur service interruptions",
                'name_de' => "",
            ]
        ]);
    }
}
