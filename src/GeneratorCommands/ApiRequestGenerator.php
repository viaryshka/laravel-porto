<?php

namespace AdminKit\Porto\GeneratorCommands;

class ApiRequestGenerator extends AbstractGeneratorCommand
{
    protected $name = 'make:porto-api-request';

    protected $description = 'Create a new Request class';

    protected $type = 'RequestDTO';

    protected $stubName = 'api.request.stub';

    protected $folderInsideContainer = 'UI/API/RequestDTO';

    protected function getNameInput()
    {
        return parent::getNameInput().'RequestDTO';
    }
}
