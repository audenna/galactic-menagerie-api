<?php

namespace Tests\Unit;

use App\DTOs\Enclosure\CreateEnclosureDTO;
use App\Models\Enclosure;
use App\Repositories\Enclosure\EnclosureRepository;
use App\Services\Enclosure\EnclosureService;
use Mockery;
use Tests\TestCase;

class EnclosureServiceUnitTest extends TestCase
{
   protected EnclosureRepository $repository;
   protected EnclosureService $service;

   public function setUp(): void {
       parent::setUp();

       // Mock the repository
       $this->repository = Mockery::mock(EnclosureRepository::class);

       // inject the mocked repository into the service
       $this->service = new EnclosureService($this->repository);
   }

   public function tearDown(): void
   {
       Mockery::close();
       parent::tearDown();
   }

   public function test_it_creates_an_enclosure_successfully(): void
   {
       $payload = [
           'name' => 'Volcanic Dome',
           'type' => 'Volcanic',
           'capacity' => 10,
       ];

       $dto = CreateEnclosureDTO::fromRequest($payload);

       $expectedEnclosure = new Enclosure([
           'name' => $dto->name,
           'type' => $dto->type,
           'capacity' =>  $dto->capacity,
       ]);

       // Repository should receive create and return our expected Enclosure
       $this->repository
           ->shouldReceive('create')
           ->once()
           ->with($payload)
           ->andReturn($expectedEnclosure);

       $result = $this->service->create($dto);

       $this->assertInstanceOf(Enclosure::class, $result);
       $this->assertEquals($expectedEnclosure, $result);
       $this->assertEquals(10, $dto->capacity);
   }
}
