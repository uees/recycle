<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Shipment;


class ShipmentPolicy
{
    public function create(User $user)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }

    public function update(User $user, Shipment $shipment)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }

    public function delete(User $user, Shipment $shipment)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }
}
