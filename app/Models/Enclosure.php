<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enclosure extends Model
{
    protected $guarded = [];

    protected function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ucwords($value),
            set: fn ($value) => strtolower($value)
        );
    }

    protected function type(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ucwords($value),
            set: fn ($value) => strtolower($value)
        );
    }

    public function capacity(): int
    {
        return $this->attributes['capacity'];
    }

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    public function isFull(): bool
    {
        return $this->animals()->count() >= $this->capacity;
    }
}
