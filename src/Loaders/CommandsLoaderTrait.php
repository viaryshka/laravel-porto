<?php

namespace AdminKit\Porto\Loaders;

use Illuminate\Support\Facades\File;

trait CommandsLoaderTrait
{
    use PathsLoaderTrait;

    public function loadCommandsFromContainers($containerPath): void
    {
        $shipCommandsDirectory = $containerPath.'/Commands';
        $containerCommandsDirectory = $containerPath.'/UI/CLI/Commands';
        $this->loadTheConsoles($shipCommandsDirectory);
        $this->loadTheConsoles($containerCommandsDirectory);
    }

    private function loadTheConsoles($directory): void
    {
        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $consoleFile) {
                // Do not load route files
                if (! $this->isRouteFile($consoleFile)) {
                    $consoleClass = $this->getClassFullNameFromFile($consoleFile->getPathname());
                    // When user from the Main Service Provider, which extends Laravel
                    // service provider you get access to `$this->commands`
                    $this->commands([$consoleClass]);
                }
            }
        }
    }

    private function isRouteFile($consoleFile): bool
    {
        return $consoleFile->getFilename() === 'closures.php';
    }
}
