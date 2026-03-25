# Laravel Typesense Tools

This package adds Artisan commands that help manage Typesense collections and aliases in Laravel applications using Scout.

## Requirements

- PHP `^8.2`
- Laravel `>=10.0`
- Laravel Scout `>=10.0`
- Typesense PHP client `>=5.1`

## Installation

```bash
composer require synergitech/laravel-typesense-tools
```

## What this package provides

- `search:setup` to ensure collections exist and (optionally) import data.
- `search:switch-alias` to point aliases at the current index collections.
- `search:delete-index {suffix}` to remove old suffixed collections.
- `search:cleanup` to remove collections that are not referenced by an alias.

## Configuration expectations

This package reads model configuration from:

- `config('scout.typesense.model-settings')`

For each configured model, define settings like:

- `name` (typically `Model::indexableAs()`)
- `collection-schema` (Typically `User::getCollectionSchema()`)
- `search-parameters.query_by` (comma-separated searchable fields)

See the full examples in [`examples/models/User.php`](examples/models/User.php) and [`examples/config/scout.php`](examples/config/scout.php).

Example `config/scout.php` shape:

```php
use App\Models\User;

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
