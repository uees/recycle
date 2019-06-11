<?php

namespace App\Policies;

use App\Models\QcRecord;
use App\Models\User;


class QcRecordPolicy
{
    public function create(User $user)
    {
        return $user->hasRole(['stuff', 'iqc']);
    }

    public function update(User $user, QcRecord $record)
    {
        return $user->hasRole('iqc');
    }

    public function delete(User $user, QcRecord $record)
    {
        return $user->hasRole('iqc');
    }
}
