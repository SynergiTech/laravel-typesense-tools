<?php

namespace SynergiTech\LaravelTypesenseTools\Macros;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TypesenseSearchMacro
{
    /**
     * @template TModel of Model
     * @param Builder<TModel> $builder
     * @return Builder<TModel>
     */
    public function __invoke(Builder $builder, ?string $searchTerm = null): Builder
    {
        if (! $searchTerm) {
            return $builder;
        }

        /** @var ?Model $model */
        $model = $builder->getModel();
        throw_if(! $model, 'Unable to resolve model');
        throw_if(! method_exists($model, 'search'), 'Model does not support scout search');

        $hits = [];
        $page = 1;
        $infix = config('scout.typesense.model-settings.' . $model::class . '.search-parameters.infix');

        do {
            $options = [
                'page' => $page,
                'per_page' => 250,
            ];

            if ($infix !== null) {
                $options['infix'] = $infix;
            }

            $response = $model->search($searchTerm)
                ->options($options)
                ->raw();

            $currentHits = $response['hits'] ?? [];

            if (empty($currentHits)) {
                break;
            }

            $hits = array_merge($hits, array_filter(array_map(fn ($h) => is_numeric($h['document']['id']) ? $h['document']['id'] : null, $currentHits)));

            $page++;

            if ($page > 8) {
                break;
            }
        } while (count($currentHits) === 250);

        if (empty($hits)) {
            return $builder->whereRaw('0 = 1');
        }

        return $builder->joinSub('VALUES ROW(' . implode('),ROW(', $hits) . ')', 'typesense_results', 'typesense_results.column_0', '=', $model->qualifyColumn('id'));
    }
}
