<?php

namespace SynergiTech\LaravelTypesenseTools\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\TypesenseEngine;
use Typesense\Exceptions\ObjectNotFound;

class Setup extends Command
{
    protected $signature = 'search:setup {--F|flush} {--O|only-index}';

    protected $description = 'Set up the search database';

    public function handle(): int
    {
        /** @var array<string, mixed> $models */
        $models = config('scout.typesense.model-settings');

        $this->info('Verifying collections exist...');
        foreach (array_keys($models) as $model) {
            $this->verifyCollectionExists($model);
        }

        if ($this->option('only-index')) {
            return Command::SUCCESS;
        }

        if ($this->option('flush')) {
            foreach ($models as $model => $settings) {
                $this->call('scout:flush', [
                    'model' => $model,
                ]);
            }
        }

        foreach ($models as $model => $settings) {
            $this->call('scout:import', [
                'model' => $model,
            ]);
        }

        dispatch(function () {
            Artisan::call('search:switch-alias');
        });

        return Command::SUCCESS;
    }

    private function verifyCollectionExists(string $model): void
    {
        $model = new $model();
        /** @var TypesenseEngine $typesense */
        $typesense = app(EngineManager::class)->driver('typesense');

        if (! method_exists($model, 'indexableAs')) {
            $this->error('Please ensure the indexableAs method is implemented in ' . $model::class);
            return;
        }
        /** @phpstan-ignore-next-line */
        $index = $typesense->getCollections()->{$model->indexableAs()};

        try {
            $index->retrieve();
        } catch (ObjectNotFound) {
            $this->info('Creating collection as did not exist for ' . $model::class);
            $schema = config('scout.typesense.model-settings.' . $model::class . '.collection-schema') ?? [];
            /** @phpstan-ignore-next-line */
            $typesense->getCollections()->create($schema);
        }
    }
}
