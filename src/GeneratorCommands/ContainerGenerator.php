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
            //['filament2', 'f', InputOption::VALUE_NONE, 'Add filament v2 resource'],
            ['filament3', 'F', InputOption::VALUE_NONE, 'Add filament v3 resource'],
        ];
    }

    public function handle()
    {
        // without using parent::handle();

        $arguments = [
            'name' => Str::ucfirst(Str::singular($this->argument('name'))),
            'container' => $this->argument('name'),
            'folder' => $this->argument('folder'),
        ];

        $this->call('make:porto-migration', $arguments);
        $this->call('make:porto-model', $arguments);
        $this->call('make:porto-factory', $arguments);
        $this->call('make:porto-api-controller', [...$arguments, '--actions' => true]);
        $this->call('make:porto-api-routes', $arguments);

        if ($this->option('filament3')) {
            $this->call('make:porto-filament-resource', $arguments);
        }

        $this->makeProviders();
    }

    protected function makeProviders()
    {
        $this->addArgument(name: 'container', default: Str::ucfirst(Str::singular($this->argument('name'))));
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

        collect(multiselect('Would you like any of the following?', [
            'filament' => 'Filament',
        ]))->each(function ($option) use ($input) {
            if ($option === 'filament') {
                $filamentVersion = select(
                    label: 'Which version of filament would you like?',
                    options: [
                        //'filament2' => 'Filament v2',
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
