<?php

namespace Tests\Feature\Enclosure;

use App\Models\Enclosure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnclosureTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_can_be_deleted(): void
    {
        $enclosure = Enclosure::factory()->create();

        $response = $this->deleteJson("$this->baseUrl/enclosures/$enclosure->id");

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('enclosures', ['id' => $enclosure->id]);
    }
}
