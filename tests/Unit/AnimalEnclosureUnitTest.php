<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalEnclosureUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_knows_when_capacity_is_full(): void
    {
        /** @var Enclosure $enclosure */
        $enclosure = Enclosure::factory()->create();

        // Create animals based on the capacity of the enclosure available
        for ($i = 0; $i < $enclosure->capacity; $i++) {
            Animal::factory()->create(['enclosure_id' => $enclosure->id]);
        }

        $this->assertTrue($enclosure->isFull());
    }

    public function test_it_knows_when_capacity_is_not_full(): void
    {
        /** @var Enclosure $enclosure */
        $enclosure = Enclosure::factory()->create([
            'capacity' => 2,
        ]);

        Animal::factory()->create(['enclosure_id' => $enclosure->id]);

        $this->assertFalse($enclosure->isFull());
    }
}
