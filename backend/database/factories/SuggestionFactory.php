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
            'category' => $this->faker->randomElement(['Workplace', 'Technology', 'Process Improvement', 'Employee Welfare']),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected', 'Implemented']),
            'admin_remarks' => $this->faker->optional()->sentence(),
        ];
    }
}
