<?php

namespace App\Models;


trait FieldTrait
{
    protected $guarded = ['id'];

    protected $hidden = ['deleted_at', 'extra'];
}
