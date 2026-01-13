<?php

namespace App\DTOs\Animal;

class TransferAnimalDTO
{
    public function __construct(
        public int $animal_id,
        public int $target_enclosure_id
    ) { }
}
