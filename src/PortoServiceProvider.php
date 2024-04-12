<?php

namespace AdminKit\Porto;

use AdminKit\Porto\Commands\PortoInstallCommand;
use AdminKit\Porto\GeneratorCommands\ApiActionGenerator;
use AdminKit\Porto\GeneratorCommands\ApiControllerGenerator;
use AdminKit\Porto\GeneratorCommands\ApiDTOGenerator;
use AdminKit\Porto\GeneratorCommands\ApiRequestGenerator;
use AdminKit\Porto\GeneratorCommands\ApiResourceGenerator;
use AdminKit\Porto\GeneratorCommands\ApiRoutesGenerator;
use AdminKit\Porto\GeneratorCommands\ContainerGenerator;
use AdminKit\Porto\GeneratorCommands\FactoryGenerator;
use AdminKit\Porto\GeneratorCommands\FilamentResourceGenerator;
use AdminKit\Porto\GeneratorCommands\MigrationGenerator;
use AdminKit\Porto\GeneratorCommands\ModelGenerator;
use AdminKit\Porto\GeneratorCommands\ProviderGenerator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PortoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('porto')
            ->hasCommands([
                PortoInstallCommand::class,

                ProviderGenerator::class,
                MigrationGenerator::class,
                ModelGenerator::class,
                FactoryGenerator::class,
                ApiControllerGenerator::class,
                ApiRoutesGenerator::class,
                ApiResourceGenerator::class,
                ApiRequestGenerator::class,
                FilamentResourceGenerator::class,
                ContainerGenerator::class,
                ApiActionGenerator::class,
                ApiDTOGenerator::class,
            ])
            ->hasConfigFile();
    }

    public function bootingPackage(): void
    {
        //
    }

    public function registeringPackage(): void
    {
        //
    }
}
