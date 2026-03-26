<?php

namespace SynergiTech\LaravelTypesenseTools\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\TypesenseEngine;
use RuntimeException;

class DeleteIndex extends Command
{
    protected $signature = 'search:delete-suffix {suffix}';

    protected $description = 'Delete previous search index collection';

    public function handle(): int
    {
        /** @var array<string, mixed> $models */
        $models = config('scout.typesense.model-settings');

        /** @var TypesenseEngine $typesense */
        $typesense = app(EngineManager::class)->driver('typesense');

        /** @var string $suffix */
        $suffix = $this->argument('suffix');

        $collections = $typesense->getCollections();

        /** @var array{aliases:array<array{collection_name:string}>}|array<array{collection_name:string}> $aliasesResponse */
        $aliasesResponse = $typesense->getAliases()->retrieve();

        $aliasedCollectionNames = collect($aliasesResponse['aliases'] ?? $aliasesResponse)
            ->pluck('collection_name')
            ->filter()
            ->values();

        foreach ($models as $model => $settings) {
            $model = new $model();

            if (! method_exists($model, 'searchableAs')) {
                throw new RuntimeException('Please ensure the searchableAs method is implemented in ' . $model::class);
            }

            $collectionName = $model->searchableAs() . '_' . $suffix;

            if ($aliasedCollectionNames->contains($collectionName)) {
                throw new RuntimeException('Cannot delete collection ' . $collectionName . ' because it is currently targeted by an alias.');
            }

            $collections[$collectionName]->delete();
        }

        return Command::SUCCESS;
    }
}
