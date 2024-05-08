<?php

namespace AdminKit\Porto\GeneratorCommands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
        ];
    }

    public function handle()
    {
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
    }
}
