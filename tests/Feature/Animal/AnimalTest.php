<?php

namespace Tests\Feature\Animal;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalTest extends TestCase
{
    use RefreshDatabase;

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

        $response->assertCreated()->assertJsonFragment(['name' => $name]);

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

        $response->assertConflict()
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

        $response->assertConflict() // 422
            ->assertJsonFragment([
                'message' => 'Enclosure has reached its maximum capacity.'
            ]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'enclosure_id' => $source->id,
        ]);
    }
}
