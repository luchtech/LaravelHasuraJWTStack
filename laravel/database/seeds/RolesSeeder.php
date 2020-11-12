<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            "name" => "admin",
            "access_level" => 100
        ]);
        Role::create([
            "name" => "user",
            "access_level" => 1
        ]);
    }
}
