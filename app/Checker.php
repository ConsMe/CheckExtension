<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checker extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function logs()
    {
        return $this->hasMany('App\Log', 'checker_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'checker_id', 'id');
    }
}
