<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(15),
            'image_path' => null,
            'user_id' => 1,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'status' => fake()->randomElement([Course::STATUS_ACTIVE, Course::STATUS_ARCHIVED]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_ACTIVE,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_ARCHIVED,
        ]);
    }
}
