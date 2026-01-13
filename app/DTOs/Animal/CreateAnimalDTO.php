<?php

namespace App\DTOs\Animal;

use App\Services\Animal\AnimalService;

readonly class CreateAnimalDTO
{
    public function __construct(
        public string $name,
        public string $species,
        public string $preferred_environment,
        public int $enclosure_id,
    ) { }

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            species: $validated['species'],
            preferred_environment: $validated['preferred_environment'],
            enclosure_id: $validated['enclosure_id'],
        );
    }
}
