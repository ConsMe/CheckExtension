<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('ifrole', function ($roles) {
            return in_array(auth()->user()->role, $roles);
        });
        Blade::if('hasaccesstocheckers', function () {
            $user = auth()->user();
            $role = $user->role;
            return $role === 'superadmin' || ($role === 'admin' && $user->has_access_to_checkers);
        });
        Blade::if('canaddeditcheckers', function () {
            $user = auth()->user();
            $role = $user->role;
            return $role === 'superadmin' || ($role === 'admin' && $user->can_add_edit_checkers);
        });
    }
}
