<?php

declare(strict_types=1);

namespace AdminKit\Porto\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PortoInstallCommand extends Command
{
    protected $signature = 'porto:install';

    protected $description = 'Install Porto package';

    public function __construct(
        private readonly Filesystem $file,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $this->copyShipFolder();
    }

    private function copyShipFolder(): void
    {
        $stubPath = __DIR__.'/../../stubs/Ship';
        $shipPath = app_path('Ship');

        if (! $this->file->isDirectory($stubPath)) {
            $this->error("The source directory {$stubPath} does not exist.");

            return;
        }

        $files = $this->file->allFiles(directory: $stubPath, hidden: true);
        foreach ($files as $file) {
            isset($n) ? $n++ : $n = 1;

            $destinationFilePath = $shipPath.'/'.$file->getRelativePathname();
            if ($this->file->exists($destinationFilePath)) {
                $this->warn("$n. File: Ship/{$file->getRelativePathname()} - exists.");

                continue;
            }

            $destinationFolderPath = dirname($destinationFilePath);
            if (! $this->file->exists($destinationFolderPath)) {
                $this->file->makeDirectory(path: dirname($destinationFilePath), recursive: true);
            }

            if (! $this->file->copy($file->getPathname(), $destinationFilePath)) {
                $this->warn("$n. File: Ship/{$file->getFilename()} - failed to copy.");
            }

            $this->info("$n. File: Ship/{$file->getRelativePathname()} - has been copied successfully.");
        }

        $this->info("\nThe Ship folder has been copied successfully.");
    }
}
