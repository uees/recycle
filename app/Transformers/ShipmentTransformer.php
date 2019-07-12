<?php

namespace App\Transformers;

use App\Models\Shipment;
use League\Fractal\TransformerAbstract;

class ShipmentTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'customer',
        'created_user',
    ];

    protected $defaultIncludes = [
        // 'created_user',
    ];

    public function transform(Shipment $shipment)
    {
        return $shipment->attributesToArray();
    }

    public function includeCustomer(Shipment $shipment)
    {
        if (!$shipment->customer()->exists()) {
            return $this->null();
        }

        return $this->item($shipment->customer, new CustomerTransformer());
    }

    public function includeCreatedUser(Shipment $shipment)
    {
        if (!$shipment->created_user()->exists()) {
            return $this->null();
        }

        return $this->item($shipment->created_user, new UserTransformer());
    }
}