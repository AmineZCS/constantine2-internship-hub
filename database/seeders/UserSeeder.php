<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // \App\Models\User::factory(1)->create([
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // create a new user with the email and password without using the User model
        \DB::table('users')->insert([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);


    }
}
