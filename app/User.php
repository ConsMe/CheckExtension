<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'api_token',
        'force_logout',
        'telegram_id',
        'telegram_auth',
        'max_undetected_errors',
        'max_uncompleted_errors',
        'has_access_to_checkers',
        'can_add_edit_checkers',
        'max_allowed_checker_tasks',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token', 'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function checkers()
    {
        return $this->hasMany('App\AdminHasChecker', 'admin_id', 'id');
    }

    public function checkerrelations()
    {
        return $this->hasMany('App\AdminHasChecker', 'checker_id', 'id');
    }

    public function checkertasks()
    {
        return $this->hasMany('App\Checker', 'checker_id', 'id');
    }

    public function logs()
    {
        return $this->hasManyThrough('App\Log', 'App\Checker', 'checker_id', 'checker_id', 'id', 'id');
    }

    public function admins()
    {
        return $this->hasManyThrough('App\User', 'App\AdminHasChecker', 'checker_id', 'id', 'id', 'admin_id');
    }
}
