<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Shipment extends Model
{
    protected $fillable = [
        'product_name',
        'product_batch',
        'spec',
        'weight',
        'amount',
    ];

    /**
     * 应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
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
