<?php

namespace App\Transformers;

use App\Models\QcRecord;
use League\Fractal\TransformerAbstract;


class QcRecordTransformer extends  TransformerAbstract
{
    public function transform(QcRecord $qcRecord)
    {
        return $qcRecord->attributesToArray();
    }
}