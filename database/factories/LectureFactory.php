<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lecture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lecture>
 */
class LectureFactory extends Factory
{
    protected $model = Lecture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->words(4, true),
            'content' => fake()->paragraphs(3, true),
            'content_type' => Lecture::CONTENT_TYPE_HTML,
            'pdf_path' => null,
            'status' => fake()->randomElement([Lecture::STATUS_ACTIVE, Lecture::STATUS_ARCHIVED]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Lecture::STATUS_ACTIVE,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Lecture::STATUS_ARCHIVED,
        ]);
    }

    public function withText(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => Lecture::CONTENT_TYPE_TEXT,
        ]);
    }
}
