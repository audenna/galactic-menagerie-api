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
        $dto = CreateAnimalDTO::fromRequest($request->validated());

        $enclosure = $this->service->create($dto);

        return ApiResponse::success(
            $enclosure,
            ResponseAlias::HTTP_CREATED,
            'A new animal has been created successfully'
        );
    }

    public function transfer(TransferAnimalRequest $request, int $animal_id)
    {
        $dto = TransferAnimalDTO::fromRequest($animal_id, $request->validated());

        $animal = $this->service->transfer($dto);

        return ApiResponse::success($animal);
    }
}
