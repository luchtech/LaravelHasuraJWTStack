<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            "name" => "admin",
            "access_level" => 1
        ]);
        DB::table('roles')->insert([
            "name" => "member",
            "access_level" => 2
        ]);
    }
}
