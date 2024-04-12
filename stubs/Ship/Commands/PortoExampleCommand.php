<?php

namespace App\Ship\Commands;

use App\Ship\Abstracts\Commands\AbstractConsoleCommand;

class PortoExampleCommand extends AbstractConsoleCommand
{
    protected $signature = 'porto:example';

    protected $description = 'Porto example command';

    public function handle()
    {
        $this->info("Your command was correctly executed\n");
    }
}
