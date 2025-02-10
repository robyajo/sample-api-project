<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        User::create([
            'name' => 'Admin',
            'email' => 'a@a.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'User',
            'email' => 'u@u.com',
            'password' => Hash::make('123456'),
            'role' => 'user',
        ]);


        // Create 100 random users
        for ($i = 0; $i < 100; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // Default password for all users
                'role' => 'user',
            ]);
        }
    }
}
