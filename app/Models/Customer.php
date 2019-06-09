<?php


namespace App\Models;


class Customer extends Base
{
    protected $fillable = [
        'name',
        'address',
        'salesman',
    ];
}