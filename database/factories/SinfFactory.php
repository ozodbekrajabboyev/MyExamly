<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sinf>
 */
class SinfFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classes = ['1-A', '1-B', '2-A', '3', '4-A', '4-B', '5-A', '5-B', '6', '7-A', '7-B', '8-A', '8-B', '9', '10', '11'];
        return [
            'name' => fake()->randomElement($classes),
        ];
    }
}
