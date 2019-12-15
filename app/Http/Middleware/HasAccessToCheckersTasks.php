<?php

namespace App\Http\Middleware;

use Closure;

class HasAccessToCheckersTasks
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
        if (in_array($role, ['superadmin', 'checker']) || ($role === 'admin' && $user->has_access_to_checkers)) {
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
