<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class User
{
    use Searchable;

    public static function searchableAs(): string
    {
        return 'users';
    }

    public static function indexableAs(): string
    {
        // inspiration for an initial migration hack, remove when aliases are in use
        if (env('APP_ENV') !== 'local' && env('TYPESENSE_VERSION_SUFFIX') === null) {
            return self::searchableAs();
        }

        // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
        return self::searchableAs() . '_' . env('TYPESENSE_VERSION_SUFFIX', 'default');
    }

    public static function getCollectionSchema(): array
    {
        return [
            'name' => self::indexableAs(),
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                ],
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    public static function typesenseQueryBy(): array
    {
        return [
            'name',
            'email',
        ];
    }
}
