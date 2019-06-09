<?php


namespace App\Models;


class Shipment extends Base
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
