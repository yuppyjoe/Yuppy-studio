<?php

namespace Database\Factories;

use App\Models\BookFormat;
use App\Models\ManufacturerTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test-only factory. Measurements stay null: they must never be invented.
 *
 * @extends Factory<ManufacturerTemplate>
 */
class ManufacturerTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_format_id' => BookFormat::factory(),
            'manufacturer_name' => fake()->company(),
            'product_code' => null,
        ];
    }
}
