<?php

namespace Tests\Unit;

use App\DTOs\Animal\CreateAnimalDTO;
use App\Exceptions\Domain\EnclosureCapacityExceededException;
use App\Exceptions\Domain\InvalidEnvironmentException;
use App\Models\Animal;
use App\Models\Enclosure;
use App\Repositories\Animal\AnimalRepositoryInterface;
use App\Repositories\Enclosure\EnclosureRepositoryInterface;
use App\Services\Animal\AnimalService;
use Mockery;
use Tests\TestCase;

class AnimalServiceTest extends TestCase
{
    private AnimalRepositoryInterface $animalRepo;
    private EnclosureRepositoryInterface $enclosureRepo;
    private AnimalService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->animalRepo = Mockery::mock(AnimalRepositoryInterface::class);
        $this->enclosureRepo = Mockery::mock(EnclosureRepositoryInterface::class);

        $this->service = new AnimalService(
            $this->animalRepo,
            $this->enclosureRepo
        );
    }

    /**
     * @throws \Throwable
     */
    public function test_it_creates_an_animal_when_rules_are_satisfied(): void
    {
        $enclosure = new Enclosure([
            'id' => 1,
            'type' => 'Jungle',
            'capacity' => 2,
        ]);

        // No animals yet → not full
        $enclosure->setRelation('animals', collect());

        $dto = new CreateAnimalDTO(
            name: 'Simba',
            species: 'Lion',
            preferred_environment: 'Jungle',
            enclosure_id: 1
        );

        $this->enclosureRepo
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($enclosure);

        $this->animalRepo
            ->shouldReceive('create')
            ->once()
            ->andReturn(new Animal(['name' => 'Simba']));

        $animal = $this->service->create($dto);

        $this->assertInstanceOf(Animal::class, $animal);
        $this->assertEquals('Simba', $animal->name);
    }

    /**
     * @throws \Throwable
     */
    public function test_it_throws_if_environment_is_invalid(): void
    {
        $this->expectException(InvalidEnvironmentException::class);

        $enclosure = new Enclosure([
            'id' => 1,
            'type' => 'Tundra',
            'capacity' => 2,
        ]);

        $dto = new CreateAnimalDTO(
            name: 'Simba',
            species: 'Lion',
            preferred_environment: 'Jungle',
            enclosure_id: 1
        );

        $this->enclosureRepo
            ->shouldReceive('findOrFail')
            ->once()
            ->andReturn($enclosure);

        $this->service->create($dto);
    }
}
