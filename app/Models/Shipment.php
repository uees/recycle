<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Shipment extends Model
{
    protected $fillable = [
        'product_name',
        'product_batch',
        'weight',
        'amount',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
