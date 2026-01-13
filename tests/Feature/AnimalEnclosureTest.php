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

        $this->assertDatabaseHas('enclosures', ['name' => $name]);
    }

    public function test_it_rejects_enclosure_with_invalid_capacity(): void
    {
        $response = $this->postJson("$this->baseUrl/enclosures", [
            'name' => 'Tiny Dome',
            'type' => 'Tundra',
            'capacity' => 0,
        ]);

        $response->assertUnprocessable(); // 422
    }

    public function test_it_can_create_an_animal_in_valid_enclosure(): void
    {
        $name = 'Simba';
        $enclosure = Enclosure::factory()->create();

        $response = $this->postJson("$this->baseUrl/animals", [
            'name' => $name,
            'species' => 'Tiger',
            'preferred_environment' => $enclosure->type,
            'enclosure_id' => $enclosure->id,
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => $name]);

        $this->assertDatabaseHas('animals', ['name' => $name]);
    }

    public function test_it_rejects_animal_in_wrong_environment(): void
    {
        $name = 'Hot Crab';
        $enclosure = Enclosure::factory()->create([
            'type' => 'Tundra',
            'capacity' => 2,
        ]);

        $response = $this->postJson("$this->baseUrl/animals", [
            'name' => $name,
            'species' => 'Magma-Crab',
            'preferred_environment' => 'Volcanics',
            'enclosure_id' => $enclosure->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment([
                'message' => 'Animal cannot survive in this enclosure environment.'
            ]);

        $this->assertDatabaseMissing('animals', ['name' => $name]);
    }

    public function test_it_rejects_animal_if_enclosure_is_full(): void
    {
        $enclosure = Enclosure::factory()->create(['capacity' => 1]);

        // Fill the enclosure
        Animal::factory()->create([
            'enclosure_id' => $enclosure->id,
            'preferred_environment' => $enclosure->type,
        ]);

        $response = $this->postJson("$this->baseUrl/animals", [
            'name' => 'Second Alien',
            'species' => 'Leafy',
            'preferred_environment' => $enclosure->type,
            'enclosure_id' => $enclosure->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment([
                'message' => 'Enclosure has reached its maximum capacity.'
            ]);
    }

    public function test_it_can_transfer_animal_to_valid_enclosure(): void
    {
        $source = Enclosure::factory()->create([
            'type' => 'Jungle',
            'capacity' => 2,
        ]);

        $target = Enclosure::factory()->create([
            'type' => 'Jungle',
            'capacity' => 2,
        ]);

        $animal = Animal::factory()->create([
            'enclosure_id' => $source->id,
            'preferred_environment' => 'Jungle',
        ]);

        $response = $this->postJson("$this->baseUrl/animals/{$animal->id}/transfer", [
            'target_enclosure_id' => $target->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['enclosure_id' => $target->id]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'enclosure_id' => $target->id,
        ]);
    }

    public function test_it_rejects_transfer_to_wrong_environment(): void
    {
        $source = Enclosure::factory()->create([
            'type' => 'Volcanic',
            'capacity' => 2,
        ]);

        $target = Enclosure::factory()->create([
            'type' => 'Tundra',
            'capacity' => 2,
        ]);

        $animal = Animal::factory()->create([
            'enclosure_id' => $source->id,
            'preferred_environment' => 'Volcanic',
        ]);

        $response = $this->postJson("$this->baseUrl/animals/{$animal->id}/transfer", [
            'target_enclosure_id' => $target->id,
        ]);

        $response->assertUnprocessable() // 422
            ->assertJsonFragment([
                'message' => 'Animal cannot survive in this enclosure environment.'
            ]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'enclosure_id' => $source->id,
        ]);
    }

    public function test_it_rejects_transfer_if_target_is_full(): void
    {
        $source = Enclosure::factory()->create([
            'type' => 'Jungle',
            'capacity' => 2,
        ]);

        $target = Enclosure::factory()->create([
            'type' => 'Jungle',
            'capacity' => 1,
        ]);

        Animal::factory()->create([
            'enclosure_id' => $target->id,
            'preferred_environment' => 'Jungle',
        ]);

        $animal = Animal::factory()->create([
            'enclosure_id' => $source->id,
            'preferred_environment' => 'Jungle',
        ]);

        $response = $this->postJson("$this->baseUrl/animals/{$animal->id}/transfer", [
            'target_enclosure_id' => $target->id,
        ]);

        $response->assertUnprocessable() // 422
            ->assertJsonFragment([
                'message' => 'Enclosure has reached its maximum capacity.'
            ]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'enclosure_id' => $source->id,
        ]);
    }
}
