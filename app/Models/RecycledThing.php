<?php


namespace App\Models;


class RecycledThing extends Base
{
    protected $fillable = [
        'amount',
        'recycled_user',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qc_records()
    {
        return $this->hasMany(QcRecord::class);
    }
}
