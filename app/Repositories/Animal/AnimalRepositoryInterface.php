<?php

namespace App\Repositories\Animal;

use App\Models\Animal;

interface AnimalRepositoryInterface
{
    public function moveToEnclosure(Animal $animal, int $enclosureId): Animal;

    public function countByEnclosure(int $enclosureId): int;
}
