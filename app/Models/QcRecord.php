<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class QcRecord extends Model
{
    protected $fillable = [
        'bad_amount',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recycled_thing()
    {
        return $this->belongsTo(RecycledThing::class, 'recycled_thing_id');
    }
}
