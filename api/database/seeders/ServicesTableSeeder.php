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
                'name_en' => "Garbage calendar",
                'name_de' => "",
            ],
            [
                'name_en' => "Gemeinde news",
                'name_de' => "",
            ],
            [
                'name_en' => "Weather notifications",
                'name_de' => "",
            ]
        ]);
    }
}
