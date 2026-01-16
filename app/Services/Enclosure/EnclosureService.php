<?php

namespace App\Services\Enclosure;

use App\DTOs\Enclosure\CreateEnclosureDTO;
use App\Exceptions\Domain\InvalidEnclosureCapacityException;
use App\Exceptions\Domain\InvalidResourceIdException;
use App\Models\Enclosure;
use App\Logging\DomainLogger;
use App\Repositories\Enclosure\EnclosureRepository;
use function PHPUnit\Framework\isNumeric;

readonly class EnclosureService
{
    public function __construct(
        private EnclosureRepository $enclosureRepository,
    ) {}

    public function create(CreateEnclosureDTO $dto): Enclosure
    {
        if ($dto->capacity < 1) {
            DomainLogger::error("Invalid capacity for enclosure", (array)json_encode($dto));

            throw new InvalidEnclosureCapacityException();
        }

        $enclosure = $this->enclosureRepository->create([
            'name' => $dto->name,
            'type' => $dto->type,
            'capacity' => $dto->capacity,
        ]);

        DomainLogger::alert('Enclosure created', (array) $enclosure);

        return $enclosure;
    }

    public function destroy(int|string $enclosure_id): bool
    {
        if (! isNumeric($enclosure_id)) {
            throw new InvalidResourceIdException();
        }

        $enclosure = $this->enclosureRepository->findOrFail($enclosure_id);

        return $this->enclosureRepository->delete($enclosure);
    }
}
