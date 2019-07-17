<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];

    public function checker()
    {
        return $this->belongsTo('App\Checker');
    }
}
