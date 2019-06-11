<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RecycledThing;


class RecycledThingPolicy
{
    public function recycle(User $user)
    {
        return $user->hasRole(['finished_warehouse_keeper', 'material_warehouse_keeper']);
    }

    public function update(User $user, RecycledThing $recycledThing)
    {
        return $user->hasRole(['finished_warehouse_keeper', 'material_warehouse_keeper']);
    }

    public function confirm(User $user, RecycledThing $recycledThing)
    {
        return $user->hasRole('material_warehouse_keeper');
    }

    public function delete(User $user, RecycledThing $recycledThing)
    {
        // 没有确认前，成品仓可以删
        if (!$recycledThing->confirmed_at) {
            return $user->hasRole('finished_warehouse_keeper');
        }

        return false;
    }
}
