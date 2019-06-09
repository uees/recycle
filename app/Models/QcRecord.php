<?php


namespace App\Models;


class QcRecord extends Base
{
    protected $fillable = [
        'bad_amount',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(RecycledThing::class, 'recycled_thing_id');
    }
}
