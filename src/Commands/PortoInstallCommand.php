<?php

declare(strict_types=1);

namespace AdminKit\Porto\Commands;

use AdminKit\Porto\Actions\AppendRowToFileArrayAction;
use AdminKit\Porto\DTO\AppendRowToFileDTO;
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

        $this->importShipProvider();
    }

    private function copyShipFolder(): void
    {
        $this->info("Copying Ship folder...\n");

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

        $this->info("\nThe Ship folder has been copied successfully.\n");
    }

    private function importShipProvider(): void
    {
        $this->info("Importing ShipProvider...\n");

        // laravel 11
        $importFilePath = base_path('bootstrap/providers.php');
        $appendRowDTO = new AppendRowToFileDTO(
            appendRow: 'App\\Ship\\Providers\\ShipProvider::class,',
            destinationFilePath: $importFilePath,
        );

        // support laravel 10
        if (! $this->file->exists(base_path('bootstrap/providers.php'))) {
            $importFilePath = config_path('app.php');
            $appendRowDTO = new AppendRowToFileDTO(
                appendRow: 'App\Ship\Providers\ShipProvider::class',
                destinationFilePath: $importFilePath,
                beforeAppendRow: "'providers' => ServiceProvider::defaultProviders()->merge([",
                AfterAppendRow: '])->toArray(),'
            );
        }

        $result = app(AppendRowToFileArrayAction::class)->run($appendRowDTO);

        if ($result === true) {
            $this->info("The ShipProvider has already imported.\n");

            return;
        }

        if ($result === false) {
            $this->error("Failed to import the ShipProvider.\n");

            return;
        }

        $this->info("The ShipProvider has been imported successfully.\n");
    }
}
