<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnteringWarehouse extends Model
{
    protected $fillable = [
        'product_name',
        'product_batch',
        'recyclable_type',
        'spec',
        'weight',
        'amount',
        'entered_at',
        'made_at',
    ];

    /**
     * 应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'entered_at',
        'made_at',
    ];
}