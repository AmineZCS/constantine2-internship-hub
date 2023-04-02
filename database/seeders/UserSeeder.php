<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // User::create([
        //     'email' => 'test4@gmail.com',
        //     'password' => bcrypt('password'),
        // ]);

        User::factory()->count(1)->create();


        // create a new user with the email and password without using the User model
        // \DB::table('users')->insert([
        //     'email' => 'test4@example.com',
        //     'password' => bcrypt('password'),
        // ]);


    }
}
