<?php

namespace Database\Seeders;

use App\Models\Occasion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OccasionSeeder extends Seeder
{
    public const OCCASIONS = [
        'Wedding' => 'The day, the people, the vows.',
        'Engagement' => 'The yes, and everything that led to it.',
        'Graduation' => 'Years of work, one proud moment.',
        'Birthday' => 'Another year, beautifully kept.',
        'Family' => 'The people who make home.',
        'Travel' => 'Places, light and long roads.',
        'Corporate Portfolio' => 'Your work, presented with intent.',
        'Baby/Newborn' => 'The first days, held forever.',
    ];

    public function run(): void
    {
        $order = 0;

        foreach (self::OCCASIONS as $name => $description) {
            Occasion::updateOrCreate(
                ['slug' => Str::slug(str_replace('/', ' ', $name))],
                ['name' => $name, 'description' => $description, 'sort_order' => ++$order, 'is_active' => true],
            );
        }
    }
}
