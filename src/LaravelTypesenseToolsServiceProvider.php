<?php

namespace SynergiTech\LaravelTypesenseTools;

use Illuminate\Support\ServiceProvider;
use SynergiTech\LaravelTypesenseTools\Console\Commands\CleanupTypesenseCollections;
use SynergiTech\LaravelTypesenseTools\Console\Commands\DeleteIndex;
use SynergiTech\LaravelTypesenseTools\Console\Commands\Setup;
use SynergiTech\LaravelTypesenseTools\Console\Commands\SwitchAlias;

class LaravelTypesenseToolsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Setup::class,
                SwitchAlias::class,
                DeleteIndex::class,
                CleanupTypesenseCollections::class,
            ]);
        }
    }
}
