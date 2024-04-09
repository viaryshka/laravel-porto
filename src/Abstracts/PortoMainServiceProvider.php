<?php

namespace AdminKit\Porto\Abstracts;

use AdminKit\Porto\Loaders\AutoLoaderTrait;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

abstract class PortoMainServiceProvider extends LaravelServiceProvider
{
    use AutoLoaderTrait;

    protected array $serviceProviders = [];

    public function register(): void
    {
        $this->registerContainer();
    }

    public function boot(): void
    {
        $this->bootContainer();
    }
}
