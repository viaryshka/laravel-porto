<?php

namespace AdminKit\Porto\GeneratorCommands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * @named-arguments-supported 'name', 'container', 'folder'
 */
class ContainerGenerator extends AbstractGeneratorCommand
{
    protected $name = 'make:porto-container';

    protected $description = 'Create a new Container';

    protected $type = 'Container';

    protected $folderInsideContainer = 'Providers';

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
            ['folder', InputArgument::OPTIONAL, 'The folder name', 'Containers'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_NONE, 'Add Model files'],
            ['api', 'a', InputOption::VALUE_NONE, 'Add API files'],
            ['filament2', 'f', InputOption::VALUE_NONE, 'Add Filament v2 resource'],
            ['filament3', 'F', InputOption::VALUE_NONE, 'Add new Filament v3 resource'],
        ];
    }

    public function handle()
    {
        if ($this->option('filament2') && $this->option('filament3')) {
            $this->error('You can choose only one Filament version, --filament2 or --filament3');

            return;
        }

        $name = Str::ucfirst(Str::singular(Str::camel($this->argument('name'))));
        $this->addArgument(name: 'container', default: $name);

        // without using parent::handle();

        $arguments = [
            'name' => $name,
            'container' => $this->argument('container'),
            'folder' => $this->argument('folder'),
        ];

        $this->makeProviders();

        if ($this->option('model')) {
            $this->call('make:porto-migration', $arguments);
            $this->call('make:porto-model', $arguments);
            $this->call('make:porto-factory', $arguments);
        }

        if ($this->option('api')) {
            $this->call('make:porto-api-controller', [...$arguments, '--actions' => true]);
            $this->call('make:porto-api-routes', $arguments);
        }

        if ($this->option('filament2')) {
            $this->call('make:porto-filament2-resource', $arguments);
        }

        if ($this->option('filament3')) {
            $this->call('make:porto-filament-resource', $arguments);
        }
    }

    protected function makeProviders(): void
    {
        $this->makeFileInContainer('Providers/MainServiceProvider.php', 'main.service.provider.stub');
        $this->importMainProviderToShipProvider();
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $input->setArgument('folder', text(
            label: 'Would you like to specify a custom folder? (Optional)',
            default: $this->argument('folder'),
        ));

        if ($this->didReceiveOptions($input)) {
            return;
        }

        collect(multiselect(
            label: 'Would you like any of the following?',
            options: [
                'model' => 'Model',
                'api' => 'API',
                'filament' => 'Filament',
            ],
            default: ['model', 'api'],
        ))->each(function ($option) use ($input) {
            if ($option === 'filament') {
                $filamentVersion = select(
                    label: 'Which version of filament would you like?',
                    options: [
                        'filament2' => 'Filament v2',
                        'filament3' => 'Filament v3',
                    ],
                    default: 'filament3',
                );
                $input->setOption($filamentVersion, true);

                return;
            }

            $input->setOption($option, true);
        });
    }
}
