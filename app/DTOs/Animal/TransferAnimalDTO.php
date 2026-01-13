<?php

namespace App\DTOs\Animal;

class TransferAnimalDTO
{
    public function __construct(
        public int $animal_id,
        public int $target_enclosure_id
    ) { }

    public static function fromRequest(int $animal_id, array $validated): self
    {
        return new self(
            animal_id: $animal_id,
            target_enclosure_id: $validated['target_enclosure_id']
        );
    }
}
