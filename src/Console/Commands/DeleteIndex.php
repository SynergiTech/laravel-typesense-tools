<?php

namespace SynergiTech\LaravelTypesenseTools\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\TypesenseEngine;
use Throwable;

class DeleteIndex extends Command
{
    protected $signature = 'search:delete-index {suffix}';

    protected $description = 'Delete previous search index collection';

    public function handle(): int
    {
        /** @var array<string, mixed> $models */
        $models = config('scout.typesense.model-settings');

        /** @var TypesenseEngine $typesense */
        $typesense = app(EngineManager::class)->driver('typesense');

        /** @var string $suffix */
        $suffix = $this->argument('suffix');

        foreach ($models as $model => $settings) {
            $model = new $model();

            if (! method_exists($model, 'searchableAs')) {
                $this->error('Please ensure the searchableAs method is implemented in ' . $model::class);
                continue;
            }

            try {
                $typesense->getCollections()->{($model->searchableAs() . '_' . $suffix)}->delete();
            } catch (Throwable $e) {
                $this->info('Failed to delete collection ' . $model->searchableAs() . '_' . $suffix . ': ' . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
