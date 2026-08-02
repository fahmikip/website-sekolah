<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'news_category_id' => NewsCategory::factory(), 'author_id' => User::factory(),
            'title' => $title, 'slug' => Str::slug($title), 'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true), 'status' => 'published',
            'is_featured' => false, 'published_at' => now()->subDay(),
            'meta_title' => $title, 'meta_description' => fake()->sentence(12),
        ];
    }
}
