<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //\App\Models\User::factory(10)->create();

        for ($i=0; $i < 10; $i++) {
             \App\Models\User::factory()->create([
                 'name' => 'User ' . $i,
                 'email' => 'user_' . $i . '@todo.local',
                 'email_verified_at' => now(),
                 'remember_token' => Str::random(10),
                 'password' => '$2y$10$LCdzQbPcBfRYuEkwVsLS9OEY5BjUTXGv/1CePvcI1HiRd2X/t8Bye',
             ]);
        }
    }
}
