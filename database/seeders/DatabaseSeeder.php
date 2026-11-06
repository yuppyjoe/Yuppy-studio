<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Reference data: safe for every environment, idempotent.
            OccasionSeeder::class,
            DesignFamilySeeder::class,
            BookFormatSeeder::class,
            YuppyBrandingSeeder::class,
            AdminSeeder::class,
        ]);

        // Demo accounts are never created in production.
        if (! app()->isProduction()) {
            $this->call(DemoAccountsSeeder::class);
        }

        // ManufacturerTemplate rows are intentionally NOT seeded: real
        // measurements must come from manufacturer spec sheets.
    }
}
