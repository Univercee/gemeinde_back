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
                'name_en' => "Gemeinde news",
                'name_de' => "",
            ],
            [
                'id' => 3,
                'name_en' => "Weather notifications",
                'name_de' => "",
            ]
        ]);
    }
}
