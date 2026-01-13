<?php

namespace App\DTOs\Enclosure;

readonly class CreateEnclosureDTO
{
    public function __construct(
        public string $name,
        public string $type,
        public int $capacity
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            type: $validated['type'],
            capacity: $validated['capacity']
        );
    }
}
