<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'role' => 'Employee',
            'bio' => 'A senior developer demo account.',
            'PhoneNumber' => '+1234567890',
            'Title' => 'Software Engineer'
        ]);
    }
}
