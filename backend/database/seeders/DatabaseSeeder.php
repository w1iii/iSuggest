<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('123'),
            'role' => 'Super Administrator',
        ]);

        User::factory()->create([
            'name' => 'Jane',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'),
            'role' => 'Administrator',
        ]);

        User::factory()->create([
            'name' => 'John',
            'email' => 'employee@example.com',
            'password' => Hash::make('123'),
            'role' => 'Employee',
        ]);

        $this->call(SuggestionSeeder::class);
    }
}