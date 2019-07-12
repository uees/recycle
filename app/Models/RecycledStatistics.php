<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RecycledStatistics extends Model
{
    protected $table = 'recycled_statistics';

    protected $fillable = [
        'recyclable_type',
        'year',
        'month',
        'entering_warehouse_amount',
        'shipment_amount',
        'recycled_amount',
        'bad_amount',
        'good_amount',
    ];
}