<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'name' => $title,
            'department_id' => Department::factory(),
            'title' => $title,
            'description' => fake()->sentence(),
        ];
    }
}
