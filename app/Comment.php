<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['text', 'parent_id'];

    /**
     * The attributes that are hidden from json fields.
     *
     * @var array
     */
    protected $hidden = [];
}
