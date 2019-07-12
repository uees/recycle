<?php

namespace App\Transformers;

use App\Models\RecycledThing;
use League\Fractal\TransformerAbstract;

class RecycledThingTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'customer',
        'confirmed_user',
        'qc_records',
    ];

    public function transform(RecycledThing $recycledThing)
    {
        return $recycledThing->attributesToArray();
    }

    public function includeCustomer(RecycledThing $recycledThing)
    {
        return $this->item($recycledThing->customer, new CustomerTransformer());
    }

    public function includeConfirmedUser(RecycledThing $recycledThing)
    {
        return $this->item($recycledThing->confirmed_user, new UserTransformer());
    }

    public function includeQcRecords(RecycledThing $recycledThing)
    {
        return $this->collection($recycledThing->qc_records, new QcRecordTransformer());
    }
}