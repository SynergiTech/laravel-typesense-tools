# Laravel Typesense Tools

`synergitech/laravel-typesense-tools` adds Artisan commands that help manage Typesense collections and aliases in Laravel applications using Scout.

## What this package provides

- `search:setup` to ensure collections exist and (optionally) import data.
- `search:switch-alias` to point aliases at the current index collections.
- `search:delete-index {suffix}` to remove old suffixed collections.
- `search:cleanup` to remove collections that are not referenced by an alias.

## Requirements

- PHP `^8.2`
- Laravel `>=10.0`
- Laravel Scout `>=10.0`
- Typesense PHP client `>=5.1`

## Installation

```bash
composer require synergitech/laravel-typesense-tools
```

## Configuration expectations

This package reads model configuration from:

- `config('scout.typesense.model-settings')`

For each configured model, this package expects:

- `indexableAs()` (used for collection names)
- `searchableAs()` (used for alias names and deletion targets)

When creating missing collections, `search:setup` uses the model schema from:

- `scout.typesense.model-settings.<ModelClass>.collection-schema`

Example `config/scout.php` shape:

```php
'typesense' => [
    'model-settings' => [
        App\\Models\\Post::class => [
            'collection-schema' => [
                'name' => 'posts_tmp_20260325',
                'fields' => [
                    ['name' => 'id', 'type' => 'string'],
                    ['name' => 'title', 'type' => 'string'],
                ],
            ],
        ],
    ],
],
```

## Command reference

### `php artisan search:setup`

Ensures each configured collection exists. If `--only-index` is not provided, it then imports data and dispatches an alias switch.

Options:

- `--only-index`: only verify/create collections.
- `--flush`: flush each model before importing.

Examples:

```bash
php artisan search:setup --only-index
php artisan search:setup --flush
```

### `php artisan search:switch-alias`

Upserts each model's alias (`searchableAs`) to target the active collection (`indexableAs`).

```bash
php artisan search:switch-alias
```

### `php artisan search:delete-index {suffix}`

Deletes old collections named as:

- `<searchableAs>_<suffix>`

Example:

```bash
php artisan search:delete-index 20260325
```

### `php artisan search:cleanup`

Finds and optionally deletes collections not currently referenced by any alias.

```bash
php artisan search:cleanup
```
