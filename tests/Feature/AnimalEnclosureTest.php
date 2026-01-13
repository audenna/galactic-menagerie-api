<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalEnclosureTest extends TestCase
{
    use RefreshDatabase;

    private string $baseUrl = '/api/v1';

    public function test_it_can_create_an_enclosure(): void
    {
        $name = 'Volcanic Dome';
        $response = $this->postJson("$this->baseUrl/enclosures", [
            'name' => $name,
            'type' => 'Volcanic',
            'capacity' => 5,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => $name,
                'type' => 'Volcanic',
                'capacity' => 5,
            ]);

        $this->assertDatabaseHas('enclosures', ['name' => strtolower($name)]);
    }

    public function test_it_rejects_enclosure_with_invalid_capacity(): void
    {
        $response = $this->postJson("$this->baseUrl/enclosures", [
            'name' => 'Tiny Dome',
            'type' => 'Tundra',
            'capacity' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_create_an_animal_in_valid_enclosure(): void
    {
        $name = 'Fuzzy Alien';
        $enclosure = Enclosure::factory()->create();

        $response = $this->postJson("$this->baseUrl/animals", [
            'name' => $name,
            'species' => 'Flufficorn',
            'preferred_environment' => 'Jungle',
            'enclosure_id' => $enclosure->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => $name]);

        $this->assertDatabaseHas('animals', ['name' => strtolower($name)]);
    }
}
