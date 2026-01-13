<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enclosure extends Model
{
    use HasFactory;

    protected $guarded = [];

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
