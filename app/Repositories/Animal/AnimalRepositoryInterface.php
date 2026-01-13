<?php

namespace App\Repositories\Animal;

use App\Models\Animal;

interface AnimalRepositoryInterface
{
    public function findOrFail(int $id): Animal;

    public function create(array $attributes): Animal;

    public function moveToEnclosure(Animal $animal, int $enclosureId): Animal;

    public function countByEnclosure(int $enclosureId): int;
}
