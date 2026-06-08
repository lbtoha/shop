<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;

class ModalIndexQuey
{
    public static function get($model, array|string $with = [], bool $with_pagination = true, string|array $select_columns = '*')
    {
        $perPage = request()->query('per_page', config('extra_service.site_pagination_config.per_page', 10));

        $model = $model->with($with);

        $query = self::globalQuery($model);

        if (is_array($select_columns)) {
            $query = $query->select($select_columns);
        }

        return $with_pagination ? $query->paginate($perPage) : $query->get();
    }

    public static function getWithCache($model, array|string $with = [], bool $with_pagination = true, string|array $select_columns = '*', $cache_key = '')
    {
        $cacheKey = 'modal_index_query_'.md5(json_encode([
            'model' => get_class($model),
            'custom_key' => $cache_key,
            'with' => $with,
            'select_columns' => $select_columns,
            'per_page' => request()->query('per_page', 10),
            'page' => request()->query('page', 1),
        ]));

        return Cache::remember($cacheKey, config('extra_service.site_pagination_config.cache_time'), function () use ($model, $with, $with_pagination, $select_columns) {
            return self::get($model, $with, $with_pagination, $select_columns);
        });
    }

    public static function globalQuery(Builder $query)
    {
        $sort = request()->query('sort', config('extra_service.site_pagination_config.sort_type', 'desc'));

        $column = request()->query('column', 'created_at');

        $start_date = request()->query('start_date', '');

        $end_date = request()->query('end_date', '');

        $search = request()->query('search', '');

        $query = $query->orderBy($column, $sort);

        if ($search) {
            $model_object = $query->getModel();
            if (method_exists($model_object, 'getSearchAttribute')) {
                $query->whereLike($model_object->getSearchAttribute(), $search);
            }
        }

        if ($start_date && $end_date) {
            $query = $query->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay(),
            ]);
        }

        return $query;
    }
}
