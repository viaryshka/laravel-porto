<?php

namespace App\Ship\Commands;

use App\Ship\Abstracts\Commands\AbstractConsoleCommand;

class PortoCheckCommand extends AbstractConsoleCommand
{
    protected $signature = 'porto:check';

    protected $description = 'Porto check example command';

    public function handle()
    {
        $this->info("Porto package correctly installed.\n");
    }
}
