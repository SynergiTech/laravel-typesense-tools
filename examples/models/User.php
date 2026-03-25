<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class User
{
    use Searchable;

    public static function indexableAs(): string
    {
        return 'users_' . env('TYPESENSE_VERSION_SUFFIX', 'default');
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
