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
            'name' => implode(' ', $this->faker->words(2)),
            'species' => implode(' ', $this->faker->words(2)),
            'preferred_environment' => $this->faker->randomElement(['volcanic', 'tundra', 'jungle']), // lowercase to match Enclosure setter
            'enclosure_id' => null,
        ];
    }
}
