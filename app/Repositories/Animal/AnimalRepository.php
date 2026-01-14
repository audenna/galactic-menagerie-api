<?php

namespace App\Repositories\Animal;

use App\Models\Animal;
use App\Repositories\Base\EloquentBaseRepository;

class AnimalRepository extends EloquentBaseRepository implements AnimalRepositoryInterface
{
    public function __construct(Animal $model)
    {
        $this->model = $model;
    }

    public function moveToEnclosure(Animal $animal, int $enclosureId): Animal
    {
        $animal->enclosure_id = $enclosureId;
        $animal->save();

        return $animal;
    }

    public function countByEnclosure(int $enclosureId): int
    {
        return $this->model
            ->where('enclosure_id', $enclosureId)
            ->count();
    }
}
