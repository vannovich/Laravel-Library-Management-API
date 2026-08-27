<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'author_id' => Author::inRandomOrder()->first()?->id ?? Author::factory(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-fiction', 'Sci-Fi', 'Fantasy', 'Mystery']),
            'published_date' => $this->faker->date(),
            'total_copies' => $this->faker->numberBetween(1,  50),
            'available_copies' => $this->faker->numberBetween(0, 50),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
