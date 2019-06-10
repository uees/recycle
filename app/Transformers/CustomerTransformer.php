<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform(Customer $customer)
    {
        return $customer->attributesToArray();
    }
}