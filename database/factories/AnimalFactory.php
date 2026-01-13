<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'species' => $this->faker->words(2, true),
            'preferred_environment' => $this->faker->randomElement(['volcanic', 'tundra', 'jungle']), // lowercase to match Enclosure setter
            'enclosure_id' => null,
        ];
    }
}
