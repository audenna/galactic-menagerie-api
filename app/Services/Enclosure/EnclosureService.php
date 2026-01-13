<?php

namespace App\Services\Enclosure;

use App\DTOs\Enclosure\CreateEnclosureDTO;
use App\Exceptions\Domain\InvalidEnclosureCapacityException;
use App\Models\Enclosure;
use App\Repositories\Enclosure\EnclosureRepositoryInterface;

readonly class EnclosureService
{
    public function __construct(private EnclosureRepositoryInterface $enclosureRepository) {}

    /**
     * Create a new enclosure
     */
    public function create(CreateEnclosureDTO $dto): Enclosure
    {
        if ($dto->capacity < 1) {
            throw new InvalidEnclosureCapacityException();
        }

        return $this->enclosureRepository->create([
            'name'     => $dto->name,
            'type'     => $dto->type,
            'capacity' => $dto->capacity,
        ]);
    }

    /**
     * Retrieve an enclosure by ID
     */
    public function find(int $id): Enclosure
    {
        return $this->enclosureRepository->findOrFail($id);
    }

    /**
     * Check if an enclosure has remaining capacity
     */
    public function hasAvailableSpace(int $enclosureId): bool
    {
        $enclosure = $this->enclosureRepository->findOrFail($enclosureId);

        return $this->enclosureRepository->currentOccupancy($enclosureId) < $enclosure->capacity;
    }

    /**
     * Get occupancy details by enclosure ID
     */
    public function getOccupancyInfo(int $enclosureId): array
    {
        $enclosure = $this->enclosureRepository->findOrFail($enclosureId);

        $occupied = $this->enclosureRepository->currentOccupancy($enclosureId);

        return [
            'enclosure_id'   => $enclosure->id,
            'capacity'       => $enclosure->capacity,
            'total_occupied' => $occupied,
            'available'      => max(0, $enclosure->capacity - $occupied),
        ];
    }
}
