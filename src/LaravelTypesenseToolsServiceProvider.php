<?php

namespace SynergiTech\LaravelTypesenseTools;

use Illuminate\Support\ServiceProvider;
use SynergiTech\LaravelTypesenseTools\Console\Commands;

class LaravelTypesenseToolsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Setup::class,
                Commands\SwitchAlias::class,
                Commands\DeleteIndex::class,
                Commands\CleanupTypesenseCollections::class,
            ]);
        }
    }
}
