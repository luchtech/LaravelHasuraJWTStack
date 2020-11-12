<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "email" => "jluchavez@umindanao.edu.ph",
            "given_name" => "James Carlo",
            "middle_name" => "Sebial",
            "surname" => "Luchavez",
            "password" => Hash::make("12345678")
        ]);
    }
}
