<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['note'];

    /**
     * The attributes that are hidden from json fields.
     *
     * @var array
     */
    protected $hidden = [];
}
