<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminHasChecker extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'checker_id');
    }
}
