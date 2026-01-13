<?php

namespace App\Repositories\Enclosure;

use App\Models\Enclosure;
use App\Repositories\Base\EloquentBaseRepository;

class EnclosureRepository extends EloquentBaseRepository implements EnclosureRepositoryInterface
{
    public function __construct(Enclosure $model)
    {
        $this->model = $model;
    }

    public function currentOccupancy(int $id): int
    {
        return $this->model
            ->findOrFail($id)
            ->animals()
            ->count();
    }
}
