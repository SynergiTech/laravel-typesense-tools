<?php

use App\Models\User;

return [
    //    ... Other Scout config goes here

    'typesense' => [
        'model-settings' => [
            User::class => [
                'name' => User::indexableAs(),
                'collection-schema' => User::getCollectionSchema(),
                'search-parameters' => [
                    'query_by' => implode(',', User::typesenseQueryBy()),
                ],
            ],
        ],
    ],
];
