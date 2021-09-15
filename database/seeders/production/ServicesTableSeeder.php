<?php

namespace Database\Seeders\Production;

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
                'name_de' => "Abfallkalender",
                'is_everywhere' => false
            ],
            [
                'id' => 2,
                'name_en' => "News from local authority",
                'name_de' => "Nachrichten aus dem Gemeinderat",
                'is_everywhere' => false
            ],
            [
                'id' => 3,
                'name_en' => "Weather warnings",
                'name_de' => "Unwetterwarnungen",
                'is_everywhere' => false
            ],
            [
                'id' => 4,
                'name_en' => "Neighbor alerts",
                'name_de' => "Nachbarschaftsalarm",
                'is_everywhere' => true
            ],
            [
                'id' => 5,
                'name_en' => "Local events",
                'name_de' => "Lokale Veranstaltungen",
                'is_everywhere' => false
            ],
            [
                'id' => 6,
                'name_en' => "GGA Maur service interruptions",
                'name_de' => "GGA Maur Wartungsarbeiten und Störungen",
                'is_everywhere' => false
            ],
            [
                'id' => 7,
                'name_en' => "Swisscom service interruptions",
                'name_de' => "Swisscom Wartungsarbeiten und Störungen",
                'is_everywhere' => true
            ]
        ]);
    }
}
