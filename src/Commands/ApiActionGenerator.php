<?php

namespace AdminKit\Porto\Commands;

use Symfony\Component\Console\Input\InputArgument;

class ApiActionGenerator extends AbstractGeneratorCommand
{
    protected $name = 'make:porto-api-action';

    protected $description = 'Create a new Action class';

    protected $type = 'Action';

    protected $stubName = 'api.action.stub';

    protected $folderInsideContainer = 'UI/API/Actions';

    protected function getNameInput()
    {
        return parent::getNameInput().'Action';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
            ['container', InputArgument::REQUIRED, 'The container name'],
            ['folder', InputArgument::OPTIONAL, 'The folder name', 'Containers'],
            ['run-function', InputArgument::OPTIONAL, 'The run() function', "public function run()\n    {\n        //\n    }"],
            ['construct-function', InputArgument::OPTIONAL, 'The __construct() function', "public function __construct()\n    {\n        //\n    }"],
            ['use-namespaces', InputArgument::OPTIONAL, 'The use namespaces', ''],
        ];
    }

    protected function getVariables()
    {
        return [
            '{{ runFunction }}' => $this->argument('run-function'),
            '{{ constructFunction }}' => $this->argument('construct-function'),
            '{{ useNamespaces }}' => $this->argument('use-namespaces'),
        ];
    }
}
