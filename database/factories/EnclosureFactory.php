<?php

namespace Database\Factories;

use App\Models\Enclosure;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnclosureFactory extends Factory
{
    protected $model = Enclosure::class;

    public function definition(): array
    {
        return [
            'name' => implode(' ', $this->faker->words(2)),
            'type' => $this->faker->randomElement(['volcanic', 'tundra', 'jungle']),
            'capacity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
