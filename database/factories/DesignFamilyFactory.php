<?php

namespace Database\Factories;

use App\Models\DesignFamily;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DesignFamily>
 */
class DesignFamilyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
