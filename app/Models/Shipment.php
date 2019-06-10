<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Shipment extends Model
{
    protected $fillable = [
        'weight',
        'amount',
        'created_user',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
