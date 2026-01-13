<?php

namespace App\DTOs\Enclosure;

readonly class CreateEnclosureDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public int $capacity
    ) {}
}
