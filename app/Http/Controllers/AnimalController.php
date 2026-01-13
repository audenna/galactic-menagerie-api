<?php

namespace App\Http\Controllers;

use App\DTOs\Animal\CreateAnimalDTO;
use App\DTOs\Animal\TransferAnimalDTO;
use App\Http\Requests\Animal\StoreAnimalRequest;
use App\Http\Requests\Animal\TransferAnimalRequest;
use App\Responses\ApiResponse;
use App\Services\Animal\AnimalService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AnimalController extends Controller
{
    public function __construct(private readonly AnimalService $service) { }

    public function store(StoreAnimalRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $dto = new CreateAnimalDTO(
            name: $validated['name'],
            species: $validated['species'],
            preferred_environment: $validated['preferred_environment'],
            enclosure_id: $validated['enclosure_id'],
        );

        $enclosure = $this->service->create($dto);

        return ApiResponse::success($enclosure, ResponseAlias::HTTP_CREATED);
    }

    public function transfer(TransferAnimalRequest $request, int $animalId)
    {
        $dto = new TransferAnimalDTO(
            animal_id: $animalId,
            target_enclosure_id: $request->validated('target_enclosure_id')
        );

        $animal = $this->service->transfer($dto);

        return ApiResponse::success($animal);
    }
}
