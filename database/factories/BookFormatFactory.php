<?php

namespace Database\Factories;

use App\Enums\BookOrientation;
use App\Models\BookFormat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookFormat>
 */
class BookFormatFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $width = fake()->randomElement([8, 10, 12]);
        $height = $width + 2;

        return [
            'name' => "{$width}x{$height} Portrait",
            'slug' => fake()->unique()->slug(3),
            'nominal_width_in' => $width,
            'nominal_height_in' => $height,
            'orientation' => BookOrientation::Portrait,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
