<?php

namespace App\Transformers;

use App\Models\EnteringWarehouse;
use League\Fractal\TransformerAbstract;


class EnteringWarehouseTransformer extends  TransformerAbstract
{
    public function transform(EnteringWarehouse $enteringWarehouse)
    {
        return $enteringWarehouse->attributesToArray();
    }
}
