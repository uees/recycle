<?php

namespace App\Transformers;

use App\Models\RecycledStatistics;
use League\Fractal\TransformerAbstract;


class RecycledStatisticsTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'customer',
    ];

    public function transform(RecycledStatistics $statistics)
    {
        return $statistics->attributesToArray();
    }

    public function includeCustomer(RecycledStatistics $statistics)
    {
        if (!$statistics->customer()->exists()) {
            return $this->null();
        }

        return $this->item($statistics->customer, new CustomerTransformer());
    }
}
