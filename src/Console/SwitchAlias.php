<?php

namespace SynergiTech\LaravelTypesenseTools\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\TypesenseEngine;
use Throwable;

class SwitchAlias extends Command
{
    protected $signature = 'search:switch-alias';

    protected $description = 'Re-point the search alias to a new target.';

    public function handle(): int
    {
        /** @var array<string, mixed> $models */
        $models = config('scout.typesense.model-settings');
        /** @var TypesenseEngine $typesense */
        $typesense = app(EngineManager::class)->driver('typesense');

        foreach ($models as $model => $settings) {
            $model = new $model();

            if (! method_exists($model, 'indexableAs') || ! method_exists($model, 'searchableAs')) {
                $this->error('Please ensure the indexableAs and/or searchableAs methods are implemented in ' . $model::class);
                continue;
            }

            try {
                /** @phpstan-ignore-next-line */
                $typesense->getAliases()->upsert($model->searchableAs(), [
                    'collection_name' => $model->indexableAs(),
                ]);
            } catch (Throwable $e) {
                $this->error('Failed to create alias for ' . $model::class . ': ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
