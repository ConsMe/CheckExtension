<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class CanAddEditCheckers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        $role = $user->role;
        if ($role === 'superadmin' || ($role === 'admin' && $user->can_add_edit_checkers)) {
            return $next($request);
        }
        if ($request->ajax()) {
            $error = ValidationException::withMessages([
                'no' => ['У вас нет прав на выполнение этого действия. Попробуйте перегрузить страницу.']
            ]);
            throw $error;
        } else {
            return \redirect('/');
        }
    }
}
