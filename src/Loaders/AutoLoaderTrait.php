<?php

namespace AdminKit\Porto\Loaders;

trait AutoLoaderTrait
{
    use AliasesLoaderTrait;
    use CommandsLoaderTrait;
    use ConfigsLoaderTrait;
    use HelpersLoaderTrait;
    use LocalizationLoaderTrait;
    use MigrationsLoaderTrait;
    use ProvidersLoaderTrait;
    use RoutesLoaderTrait;
    use ViewsLoaderTrait;

    protected ?string $containerPath = null;

    public function getContainerPath(): string
    {
        if (is_null($this->containerPath)) {
            return $this->containerPath = realpath(dirname((new \ReflectionClass($this))->getFileName()).'/..');
        }

        return $this->containerPath;
    }

    public function registerContainer(): void
    {
        $this->loadServiceProviders();
        $this->loadConfigsFromContainers($this->getContainerPath());
        $this->loadLocalsFromContainers($this->getContainerPath());
    }

    public function bootContainer(): void
    {
        $this->runRoutesAutoLoader($this->getContainerPath());
        $this->loadMigrationsFromContainers($this->getContainerPath());
        $this->loadViewsFromContainers($this->getContainerPath());
        $this->loadHelpersFromContainers($this->getContainerPath());
        $this->loadCommandsFromContainers($this->getContainerPath());
    }
}
