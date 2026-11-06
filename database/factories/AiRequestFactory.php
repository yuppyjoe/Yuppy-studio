<?php

namespace Database\Factories;

use App\Models\AiRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiRequest>
 */
class AiRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'test',
            'status' => 'pending',
        ];
    }
}
