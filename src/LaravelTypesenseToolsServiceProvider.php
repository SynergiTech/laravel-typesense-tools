<?php

namespace SynergiTech\LaravelTypesenseTools;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use SynergiTech\LaravelTypesenseTools\Console\Commands;
use SynergiTech\LaravelTypesenseTools\Macros\TypesenseSearchMacro;

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

        Builder::macro('typesenseSearch', fn (?string $searchTerm = null) => app(TypesenseSearchMacro::class)($this, $searchTerm));
    }
}
