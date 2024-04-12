<?php

namespace AdminKit\Porto\GeneratorCommands;

use Symfony\Component\Console\Input\InputArgument;

class ApiDTOGenerator extends AbstractGeneratorCommand
{
    protected $name = 'make:porto-api-dto';

    protected $description = 'Create a new DTO class';

    protected $type = 'DTO';

    protected $stubName = 'api.dto.stub';

    protected $folderInsideContainer = 'UI/API/DTO';

    protected function getNameInput()
    {
        return parent::getNameInput().'DTO';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the '.strtolower($this->type)],
            ['container', InputArgument::REQUIRED, 'The container name'],
            ['folder', InputArgument::OPTIONAL, 'The folder name', 'Containers'],
        ];
    }
}
