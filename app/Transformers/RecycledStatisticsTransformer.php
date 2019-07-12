<?php

namespace App\Transformers;

use App\Models\RecycledStatistics;
use League\Fractal\TransformerAbstract;


class RecycledStatisticsTransformer extends TransformerAbstract
{
    public function transform(RecycledStatistics $statistics)
    {
        return $statistics->attributesToArray();
    }
}