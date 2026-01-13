<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


interface BaseRepositoryInterface
{
    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function lockAndFindOrFail(int $id): Model;

    public function all(): Collection;

    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;

    public function forceDelete(Model $model): bool;
}
