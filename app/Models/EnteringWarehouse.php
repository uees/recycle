<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnteringWarehouse extends Model
{
    protected $fillable = [
        'product_name',
        'product_batch',
        'spec',
        'weight',
        'amount',
        'entered_at',
        'made_at',
    ];
}