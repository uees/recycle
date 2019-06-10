<?php

namespace App\Transformers;

use App\Models\Shipment;
use League\Fractal\TransformerAbstract;

class ShipmentTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['customer', 'qc_record'];

    public function transform(Shipment $shipment)
    {
        return $shipment->attributesToArray();
    }

    public function includeCustomers(Shipment $shipment)
    {
        if (!$shipment->customer()) {
            return $this->null();
        }

        return $this->item($shipment->customer, new CustomerTransformer());
    }
}