<?php

namespace App\Providers;

use App\Repositories\Animal\AnimalRepository;
use App\Repositories\Animal\AnimalRepositoryInterface;
use App\Repositories\Enclosure\EnclosureRepository;
use App\Repositories\Enclosure\EnclosureRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EnclosureRepositoryInterface::class,
            EnclosureRepository::class
        );

        $this->app->bind(
            AnimalRepositoryInterface::class,
            AnimalRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
