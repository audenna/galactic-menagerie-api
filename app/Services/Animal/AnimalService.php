<?php

namespace App\Services\Animal;

use App\DTOs\Animal\CreateAnimalDTO;
use App\DTOs\Animal\TransferAnimalDTO;
use App\Exceptions\Domain\AnimalAlreadyInTargetEnclosureException;
use App\Exceptions\Domain\EnclosureCapacityExceededException;
use App\Exceptions\Domain\InvalidEnvironmentException;
use App\Models\Animal;
use App\Models\Enclosure;
use App\Repositories\Animal\AnimalRepositoryInterface;
use App\Repositories\Enclosure\EnclosureRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Logging\DomainLogger;

readonly class AnimalService
{
    public function __construct(
        private AnimalRepositoryInterface $animalRepo,
        private EnclosureRepositoryInterface $enclosureRepo
    ) {}

    /**
     * @throws \Throwable
     */
    public function create(CreateAnimalDTO $dto): Animal
    {
        DomainLogger::alert("Creating animal...", (array) $dto);
        return DB::transaction(function () use ($dto) {
            /** @var Enclosure $enclosure */
            $enclosure = $this->enclosureRepo->lockAndFindOrFail($dto->enclosure_id);

            $this->assertEnvironmentCompatible($dto->preferred_environment, $enclosure);
            $this->assertEnclosureHasCapacity($enclosure);

            $animal = $this->animalRepo->create([
                'name' => $dto->name,
                'species' => $dto->species,
                'preferred_environment' => $dto->preferred_environment,
                'enclosure_id' => $dto->enclosure_id,
            ]);

            DomainLogger::alert('Animal created', (array) $animal);

            return $animal;
        });
    }

    /**
     * @throws \Throwable
     */
    public function transfer(TransferAnimalDTO $dto): Animal
    {
        DomainLogger::alert("Transferring animal...", (array) $dto);
        return DB::transaction(function () use ($dto) {
            /** @var Animal $animal */
            $animal = $this->animalRepo->lockAndFindOrFail($dto->animal_id);

            /** @var Enclosure $target */
            $target = $this->enclosureRepo->lockAndFindOrFail($dto->target_enclosure_id);

            if ($animal->enclosure_id === $target->id) {
                throw new AnimalAlreadyInTargetEnclosureException();
            }

            $this->assertEnvironmentCompatible($animal->preferred_environment, $target);
            $this->assertEnclosureHasCapacity($target);

            $transferred = $this->animalRepo->moveToEnclosure($animal, $target->id);

            DomainLogger::alert('Animal transferred', (array) $transferred);

            return $transferred;
        });
    }

    private function assertEnclosureHasCapacity(Enclosure $enclosure): void
    {
        if ($enclosure->isFull()) {
            throw new EnclosureCapacityExceededException();
        }
    }

    private function assertEnvironmentCompatible(string $environment, Enclosure $enclosure): void
    {
        if ($environment !== $enclosure->type) {
            throw new InvalidEnvironmentException();
        }
    }
}
