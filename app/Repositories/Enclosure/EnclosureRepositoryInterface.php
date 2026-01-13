<?php

namespace App\Repositories\Enclosure;

interface EnclosureRepositoryInterface
{
    public function currentOccupancy(int $id): int;
}
