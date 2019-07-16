<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EnteringWarehouse;


class EnteringWarehousePolicy
{
    public function create(User $user)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }

    public function update(User $user, EnteringWarehouse $product)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }

    public function delete(User $user, EnteringWarehouse $product)
    {
        return $user->hasRole('finished_warehouse_keeper');
    }
}
