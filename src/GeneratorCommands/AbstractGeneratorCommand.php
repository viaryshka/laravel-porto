<?php

namespace AdminKit\Porto\GeneratorCommands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\text;

abstract class AbstractGeneratorCommand extends GeneratorCommand
{
    protected $stubName;

    protected $folderInsideContainer;

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
            ['container', InputArgument::REQUIRED, 'The container name'],
            ['folder', InputArgument::OPTIONAL, 'The folder name', 'Containers'],
        ];
    }

    protected function getStub()
    {
        return file_exists($path = base_path('stubs/porto/'.$this->stubName))
            ? $path
            : __DIR__.'/stubs/'.$this->stubName;
    }

    protected function getContainerNamespace()
    {
        return str_replace(
            '/',
            '\\',
            $this->rootNamespace().$this->argument('folder').'/'.$this->argument('container')
        );
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->getContainerNamespace().'\\'.str_replace('/', '\\', $this->folderInsideContainer);
    }

    protected function getVariables()
    {
        return [
            // '{{ search }}' => 'replace',
        ];
    }

    protected function replaceVariables(&$stub)
    {
        foreach ($this->getVariables() as $key => $value) {
            $stub = str_replace($key, $value, $stub);
        }

        return $this;
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceVariables($stub)
            ->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);
    }

    protected function makeFileInContainer($filePath, $stubName)
    {
        $stubPath = file_exists($path = base_path('stubs/porto/'.$stubName))
            ? $path
            : __DIR__.'/stubs/'.$stubName;

        $name = $this->qualifyClass($this->getNameInput());
        $stub = $this->files->get($stubPath);
        $stub = $this->replaceVariables($stub)->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $fullPath = $this->getContainerPath().DIRECTORY_SEPARATOR.$filePath;
        $this->makeDirectory($fullPath);

        if (! $this->files->exists($fullPath)) {
            $this->files->put($fullPath, $this->sortImports($stub));
            $this->components->info(sprintf('File [%s] created successfully.', $filePath));
        } else {
            $this->components->error("[$filePath] already exists.");
        }
    }

    protected function getContainerPath(string $path = ''): string
    {
        $containerName = Str::ucfirst(Str::camel($this->argument('container')));
        $containerPath = app_path($this->argument('folder').DIRECTORY_SEPARATOR.$containerName);

        if ($path) {
            $containerPath .= DIRECTORY_SEPARATOR.$path;
        }

        return $containerPath;
    }

    protected function importMainProviderToShipProvider(): void
    {
        $container = $this->argument('container');
        $shipProvider = file_get_contents(app_path('Ship/Providers/ShipProvider.php'));

        $imports = trim(
            Str::before(Str::after($shipProvider, 'namespace App\Ship\Providers;'), 'class')
        );
        $import = "use {$this->getContainerNamespace()}\Providers\MainServiceProvider as {$container}ServiceProvider;";

        if (! Str::contains($imports, $import)) {
            $shipProvider = str_replace(
                $imports,
                $imports.PHP_EOL.$import,
                $shipProvider,
            );
            file_put_contents(app_path('Ship/Providers/ShipProvider.php'), $shipProvider);
        }

        $serviceProviders = trim(
            Str::before(Str::after($shipProvider, '$serviceProviders = ['), '];')
        );
        $serviceProvider = "{$container}ServiceProvider::class";

        if (! Str::contains($serviceProviders, $serviceProvider)) {
            $trailingComma = ! str_ends_with($serviceProviders, ',') ? ',' : '';

            $shipProvider = str_replace(
                $serviceProviders,
                $serviceProviders.$trailingComma.PHP_EOL.'        '.$serviceProvider,
                $shipProvider,
            );
            file_put_contents(app_path('Ship/Providers/ShipProvider.php'), $shipProvider);
        }
    }

    protected function importCustomProviderToShipProvider(): void
    {
        $name = $this->getNameInput();
        $path = $this->getContainerPath().'/Providers/MainServiceProvider.php';
        $mainProvider = file_get_contents($path);

        $serviceProviders = trim(
            Str::before(Str::after($mainProvider, '$serviceProviders = ['), '];')
        );
        $serviceProvider = "{$name}::class";

        if (! Str::contains($serviceProviders, $serviceProvider)) {
            $trailingComma = ! str_ends_with($serviceProviders, ',') ? ',' : '';

            $mainProvider = str_replace(
                $serviceProviders,
                $serviceProviders.$trailingComma.PHP_EOL.'        '.$serviceProvider,
                $mainProvider,
            );
            file_put_contents($path, $mainProvider);
        }
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $input->setArgument('folder', text(
            label: 'Would you like to specify a custom folder? (Optional)',
            default: $this->argument('folder'),
        ));
    }
}
