<?php

namespace App\DTOs\Animal;

use App\Services\Animal\AnimalService;

readonly class CreateAnimalDTO
{
    public function __construct(
        public string $name,
        public string $species,
        public string $preferred_environment,
        public int    $enclosure_id,
    ) { }
}
