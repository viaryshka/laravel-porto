<?php

namespace AdminKit\Porto\Loaders;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\SplFileInfo;

trait RoutesLoaderTrait
{
    /**
     * Register all the containers routes files in the framework
     */
    public function runRoutesAutoLoader($containerPath): void
    {
        $this->loadApiContainerRoutes($containerPath);
        $this->loadWebContainerRoutes($containerPath);
    }

    /**
     * Register the Containers API routes files
     */
    private function loadApiContainerRoutes(string $containerPath): void
    {
        // Build the container api routes path
        $apiRoutesPath = $containerPath.'/UI/API/Routes';
        // Build the namespace from the path
        $controllerNamespace = $containerPath.'\\UI\API\Controllers';

        if (File::isDirectory($apiRoutesPath)) {
            $files = File::allFiles($apiRoutesPath);
            $files = Arr::sort($files, function ($file) {
                return $file->getFilename();
            });
            foreach ($files as $file) {
                $this->loadApiRoute($file, $controllerNamespace);
            }
        }
    }

    private function loadApiRoute($file, $controllerNamespace): void
    {
        $routeGroupArray = $this->getRouteGroup($file, $controllerNamespace);

        Route::group($routeGroupArray, function ($router) use ($file) {
            require $file->getPathname();
        });
    }

    /**
     * @param  null  $controllerNamespace
     */
    public function getRouteGroup($endpointFileOrPrefixString, $controllerNamespace = null): array
    {
        return [
            'namespace' => $controllerNamespace,
            'middleware' => $this->getMiddlewares(),
            'domain' => $this->getApiUrl(),
            // If $endpointFileOrPrefixString is a file then get the version name from the file name, else if string use that string as prefix
            'prefix' => is_string(
                $endpointFileOrPrefixString
            ) ? $endpointFileOrPrefixString : $this->getApiVersionPrefix($endpointFileOrPrefixString),
        ];
    }

    private function getMiddlewares(): array
    {
        return array_filter([
            'api',
            $this->getRateLimitMiddleware(), // Returns NULL if feature disabled. Null will be removed form the array.
        ]);
    }

    private function getRateLimitMiddleware(): ?string
    {
        $rateLimitMiddleware = null;

        if (config('porto.api.throttle.enabled')) {
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinutes(
                    config('porto.api.throttle.expires'),
                    config('porto.api.throttle.attempts')
                )->by($request->user()?->id ?: $request->ip());
            });

            $rateLimitMiddleware = 'throttle:api';
        }

        return $rateLimitMiddleware;
    }

    /**
     * @return mixed
     */
    private function getApiUrl()
    {
        return config('porto.api.url');
    }

    private function getApiVersionPrefix($file): string
    {
        $prefix = trim(config('porto.api.prefix'), " /\t\n\r\0\x0B");
        $version = config('porto.api.enable_version_prefix') ? $this->getRouteFileVersionFromFileName($file) : '';

        return $prefix.'/'.$version;
    }

    private function getRouteFileVersionFromFileName($file): string|bool
    {
        $fileNameWithoutExtension = $this->getRouteFileNameWithoutExtension($file);

        $fileNameWithoutExtensionExploded = explode('.', $fileNameWithoutExtension);

        end($fileNameWithoutExtensionExploded);

        $apiVersion = prev($fileNameWithoutExtensionExploded); // get the array before the last one

        // Skip versioning the API's root route
        if ($apiVersion === 'ApisRoot') {
            $apiVersion = '';
        }

        return $apiVersion;
    }

    private function getRouteFileNameWithoutExtension(SplFileInfo $file): mixed
    {
        return pathinfo($file->getFileName())['filename'];
    }

    /**
     * Register the Containers WEB routes files
     */
    private function loadWebContainerRoutes($containerPath): void
    {
        // build the container web routes path
        $webRoutesPath = $containerPath.'/UI/WEB/Routes';
        // build the namespace from the path
        $controllerNamespace = $containerPath.'\\UI\WEB\Controllers';

        if (File::isDirectory($webRoutesPath)) {
            $files = File::allFiles($webRoutesPath);
            $files = Arr::sort($files, function ($file) {
                return $file->getFilename();
            });
            foreach ($files as $file) {
                $this->loadWebRoute($file, $controllerNamespace);
            }
        }
    }

    private function loadWebRoute($file, $controllerNamespace): void
    {
        Route::group([
            'namespace' => $controllerNamespace,
            'middleware' => ['web'],
        ], function ($router) use ($file) {
            require $file->getPathname();
        });
    }
}
