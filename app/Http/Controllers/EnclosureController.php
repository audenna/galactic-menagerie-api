<?php

namespace App\Http\Controllers;

use App\DTOs\Enclosure\CreateEnclosureDTO;
use App\Http\Requests\Enclosure\StoreEnclosureRequest;
use App\Responses\ApiResponse;
use App\Services\Enclosure\EnclosureService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EnclosureController extends Controller
{
    public function __construct(private readonly EnclosureService $service) {}

    public function store(StoreEnclosureRequest $request): JsonResponse
    {
        $dto = CreateEnclosureDTO::fromRequest($request->validated());

        $enclosure = $this->service->create($dto);

        return ApiResponse::success(
            $enclosure,
            ResponseAlias::HTTP_CREATED,
            'Enclosure created successfully'
        );
    }
}
