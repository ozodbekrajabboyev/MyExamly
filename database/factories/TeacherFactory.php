<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Teacher::class;
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
        ];
    }

    public function withSubjects($count = 3)
    {
        return $this->afterCreating(function (Teacher $teacher) use ($count) {
            $teacher->subjects()->attach(
                \App\Models\Subject::factory()->count($count)->create()
            );
        });
    }
}
