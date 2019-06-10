<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RecycledThing extends Model
{
    protected $fillable = [
        'amount',
        'confirmed_amount',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function confirmed_user()
    {
        return $this->belongsTo(User::class, 'confirmed_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qc_records()
    {
        return $this->hasMany(QcRecord::class);
    }
}
