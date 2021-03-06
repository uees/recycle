<?php


namespace App\Api\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Routing\Controller as BaseController;
use Dingo\Api\Routing\Helpers;


class Controller extends BaseController
{
    // 接口帮助调用
    use Helpers;

    public function __construct()
    {
        // 'serializer:array', // if you want to remove data wrap
        $this->middleware('serializer');

        $user = \Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            $this->middleware('api.throttle', [
                // each route have a limit of 100 of 1 minutes
                'limit' => 100,
                'expires' => 1,
            ]);
        }
    }

    /**
     * @return int
     */
    protected function getPerPage()
    {
        $request = app('Illuminate\Http\Request');
        $perPage = (int)$request->get('per_page', config('settings.per_page', 20));
        $maxPerPage = (int)config('settings.max_per_page', 100);

        return $perPage <= $maxPerPage ? $perPage : $maxPerPage;
    }

    /**
     * @param array $limits
     * @param string $default
     * @return string
     */
    protected function getSortBy(array $limits = null, $default = 'id')
    {
        $request = app('Illuminate\Http\Request');
        $field = $request->get('sort_by', $default);

        if (empty($limits) || in_array($field, $limits)) {
            return $field;
        }

        return $default;
    }

    /**
     * @param string $default
     * @return string
     */
    protected function getOrder($default = 'desc')
    {
        $request = app('Illuminate\Http\Request');
        $order = $request->get('order', $default);

        if (in_array($order, ['asc', 'desc'])) {
            return $order;
        }

        return $default;
    }

    /**
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    protected function parseWhere(Builder &$query, array $fields)
    {
        $request = app('Illuminate\Http\Request');
        foreach ($fields as $field) {
            $value = $request->get($field, '');
            if ($value == '') {
                continue;
            }
            if (preg_match('/^date:(\d{4}-\d{2}-\d{2}),?(\d{4}-\d{2}-\d{2})?$/', $value, $matches)) {
                if (count($matches) == 2) {
                    $min = $matches[1];
                    $query->where($field, '>', $min);
                } else {
                    // count($matches) == 3
                    $min = $matches[1];
                    $max = $matches[2];
                    $query->whereBetween($field, [$min, $max]);
                }
            } elseif (str_contains($value, ',')) {
                $query->whereIn($field, explode(',', $value));
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function loadRelByQuery(Builder &$query)
    {
        $request = app('Illuminate\Http\Request');
        if ($request->filled('include')) {
            $query->with(explode(',', $request->get('include')));
        }

        return $query;
    }

    /**
     * @param Model $model
     * @return Model
     */
    protected function loadRelByModel(Model $model)
    {
        $request = app('Illuminate\Http\Request');
        if ($request->filled('include')) {
            $model->load(explode(',', $request->get('include')));
        }

        return $model;
    }
}
