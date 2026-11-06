<?php

namespace Database\Seeders;

use App\Models\DesignFamily;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DesignFamilySeeder extends Seeder
{
    public const FAMILIES = [
        'Story' => 'Narrative sequencing with room for words.',
        'Minimal' => 'Quiet layouts, generous white space.',
        'Moments' => 'Candid groupings and playful rhythm.',
        'Cinematic' => 'Wide frames and dramatic full bleeds.',
        'Signature' => 'Our most considered, editorial design.',
    ];

    public function run(): void
    {
        $order = 0;

        foreach (self::FAMILIES as $name => $description) {
            DesignFamily::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description, 'sort_order' => ++$order, 'is_active' => true],
            );
        }
    }
}
