<?php

namespace SynergiTech\LaravelTypesenseTools\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\TypesenseEngine;
use Throwable;

class CleanupTypesenseCollections extends Command
{
    protected $signature = 'search:cleanup';

    protected $description = 'Remove Typesense collections not referenced by an alias.';

    public function handle(): int
    {
        /** @var TypesenseEngine $typesense */
        $typesense = app(EngineManager::class)->driver('typesense');

        /** @phpstan-ignore-next-line */
        $collections = $typesense->getCollections();
        $collectionsResponse = $collections->retrieve();

        /** @phpstan-ignore-next-line */
        $collectionNames = collect($collectionsResponse['collections'] ?? $collectionsResponse)
            ->pluck('name')
            ->filter()
            ->values();

        if ($collectionNames->isEmpty()) {
            $this->info('No collections found.');

            return Command::SUCCESS;
        }

        /** @phpstan-ignore-next-line */
        $aliasesResponse = $typesense->getAliases()->retrieve();
        /** @phpstan-ignore-next-line */
        $aliasedCollectionNames = collect($aliasesResponse['aliases'] ?? $aliasesResponse)
            ->pluck('collection_name')
            ->filter()
            ->values();

        $unusedCollections = $collectionNames
            ->diff($aliasedCollectionNames)
            ->unique()
            ->sort()
            ->values();

        if ($unusedCollections->isEmpty()) {
            $this->info('No unused collections to delete.');

            return Command::SUCCESS;
        }

        $confirmationMessage = 'Delete the following collections: ' . $unusedCollections->implode(', ') . '?';

        if (! $this->confirm($confirmationMessage)) {
            $this->info('No collections were deleted.');

            return Command::SUCCESS;
        }

        $hasFailures = false;

        foreach ($unusedCollections as $collectionName) {
            try {
                $collections[$collectionName]->delete();
                $this->info('Deleted collection ' . $collectionName);
            } catch (Throwable $e) {
                $hasFailures = true;
                $this->error('Failed to delete collection ' . $collectionName . ': ' . $e->getMessage());
            }
        }

        return $hasFailures ? Command::FAILURE : Command::SUCCESS;
    }
}
