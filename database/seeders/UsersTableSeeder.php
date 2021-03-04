<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            [
                'id' => 1,
                'email' => "lukas.andersson@switzerland.com",
                'first_name' => "Lukas",
                'last_name' => "Andersson",
                'username' => "lukas",
                'registered_at' => null,
                'secretkey' => null,
                'key_until' => null,
                'auth_type' => null
            ],
            [
                'id' => 2,
                'email' => "josy.lindberg@switzerland.com",
                'first_name' => "Josy",
                'last_name' => "Lindberg",
                'username' => "josy",
                'registered_at' => null,
                'secretkey' => null,
                'key_until' => null,
                'auth_type' => null
            ],
            [
                'id' => 3,
                'email' => "ernst.lindberg@switzerland.com",
                'first_name' => "Ernst",
                'last_name' => "Lindberg",
                'username' => "ernst",
                'registered_at' => null,
                'secretkey' => null,
                'key_until' => null,
                'auth_type' => null
            ],
            [
                'id' => 4,
                'email' => "meyer.holm@switzerland.com",
                'first_name' => "Meyer",
                'last_name' => "Holm",
                'username' => "meyer",
                'registered_at' => null,
                'secretkey' => null,
                'key_until' => null,
                'auth_type' => null
            ]
        ]);
    }
}
