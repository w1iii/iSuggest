<?php
// Ramos June 2, 2026 changed

namespace Database\Seeders;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuggestionSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            Suggestion::factory()
                ->count(rand(1, 5))
                ->create(['user_id' => $user->id]);
        });
    }
}
// Ramos June 2, 2026 changed
