<?php

namespace Database\Seeders;

use App\Enums\BookOrientation;
use App\Models\BookFormat;
use Illuminate\Database\Seeder;

/**
 * Nominal sizes only, as named in the product range (inches, width x height).
 * No manufacturer measurements are implied or stored here.
 */
class BookFormatSeeder extends Seeder
{
    public function run(): void
    {
        $formats = [
            ['8x10-portrait', '8x10 Portrait', 8, 10, BookOrientation::Portrait],
            ['8x12-portrait', '8x12 Portrait', 8, 12, BookOrientation::Portrait],
            ['12x8-landscape', '12x8 Landscape', 12, 8, BookOrientation::Landscape],
            ['10x12-portrait', '10x12 Portrait', 10, 12, BookOrientation::Portrait],
            ['12x12-square', '12x12 Square', 12, 12, BookOrientation::Square],
            ['12x18-portrait', '12x18 Portrait', 12, 18, BookOrientation::Portrait],
        ];

        foreach ($formats as $order => [$slug, $name, $width, $height, $orientation]) {
            BookFormat::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'nominal_width_in' => $width,
                'nominal_height_in' => $height,
                'orientation' => $orientation,
                'sort_order' => $order + 1,
                'is_active' => true,
            ]);
        }
    }
}
