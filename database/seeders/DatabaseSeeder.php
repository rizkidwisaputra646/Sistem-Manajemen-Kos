<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        \App\Models\User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
            ['name' => 'Admin Kos',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            ]
        );
        \App\Models\User::firstOrCreate([
        'email' => 'rizki@gmail.com'],
            [
            'name' => 'rizki ganteng',
            'password' => bcrypt('password123'),
            'role' => 'user',
            ]
    );

        
    }
}
