<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Test>
 */
class TestFactory extends Factory
{
    protected $model = Test::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+3 months');

        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(10),
            'course_id' => Course::factory(),
            'status' => fake()->randomElement([Test::STATUS_ACTIVE, Test::STATUS_ARCHIVED]),
            'max_attempts' => fake()->numberBetween(1, 5),
            'time_limit' => fake()->randomElement([30, 45, 60, 90, 120, null]),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'randomize_questions' => fake()->boolean(60),
            'display_mode' => fake()->randomElement(['full', 'one_per_page', 'review_only']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Test::STATUS_ACTIVE,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Test::STATUS_ARCHIVED,
        ]);
    }
}
