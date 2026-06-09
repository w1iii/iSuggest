<?php
namespace Database\Factories;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuggestionFactory extends Factory
{
    protected $model = Suggestion::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(config('suggestions.categories')),
            'status' => $this->faker->randomElement(config('suggestions.statuses')),
            'admin_remarks' => $this->faker->optional()->sentence(),
        ];
    }
}
