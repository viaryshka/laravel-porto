<?php

namespace AdminKit\Porto\GeneratorCommands;

use AdminKit\Porto\Actions\AppendRowToFileArrayAction;
use AdminKit\Porto\DTO\AppendRowToFileDTO;
use Illuminate\Support\Str;

class Filament2ResourceGenerator extends AbstractGeneratorCommand
{
    protected $name = 'make:porto-filament2-resource';

    protected $description = 'Create a new Filament v2 Resource files';

    protected $type = 'Filament Resource';

    protected $stubName = 'filament2.resource.stub';

    protected $folderInsideContainer = 'UI/Filament/Resources';

    public function handle()
    {
        parent::handle();

        $this->makeFilamentResourcePage('Create');
        $this->makeFilamentResourcePage('Edit');
        $this->makeFilamentResourcePage('List');

        $this->makeAndUpdateFilamentProvider();
    }

    protected function getVariables(): array
    {
        return [
            '{{ modelName }}' => $name = $this->argument('name'),
            '{{ modelNamespace }}' => $this->getContainerNamespace().'\\Models\\'.$name,
            '{{ label }}' => $name,
            '{{ pluralLabel }}' => Str::plural($name),
            '{{ resourceNamespace }}' => $this->qualifyClass($this->getNameInput()),
            '{{ filamentId }}' => Str::lower($name),
            '{{ providerNamespace }}' => $this->getContainerNamespace().'\\Providers',
        ];
    }

    protected function getNameInput(): string
    {
        return parent::getNameInput().'Resource';
    }

    protected function makeFilamentResourcePage($page): void
    {
        $stubName = 'filament.resource.'.strtolower($page).'.stub';
        $stubPath = file_exists($path = base_path("stubs/porto/$stubName")) ? $path : __DIR__."/stubs/$stubName";

        $name = $this->qualifyClass($this->getNameInput());
        $stub = $this->files->get($stubPath);
        $stubReplaced = $this->replaceVariables($stub)
            ->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);

        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $pageFilepath = $this->laravel['path'].'/'.
            str_replace('\\', '/', $name).'/Pages/'.
            ucfirst($page).$this->argument('name').'.php';
        $this->makeDirectory($pageFilepath);

        $this->files->put($pageFilepath, $this->sortImports($stubReplaced));
    }

    private function makeAndUpdateFilamentProvider(): void
    {
        $this->makeFileInContainer('Providers/FilamentServiceProvider.php', 'filament2.service.provider.stub');

        app(AppendRowToFileArrayAction::class)->run(new AppendRowToFileDTO(
            appendRow: 'FilamentServiceProvider::class,',
            destinationFilePath: $this->getContainerPath('Providers/MainServiceProvider.php'),
            beforeAppendRow: 'protected array $serviceProviders = [',
            AfterAppendRow: '];'
        ));
    }
}
