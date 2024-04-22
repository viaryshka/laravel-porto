<?php

namespace AdminKit\Porto\GeneratorCommands;

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
}
