<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if ($role === 'instructor' && Auth::user()->role !== 'instructor') {
            abort(403, 'Доступ запрещён. Только для преподавателей.');
        }

        return $next($request);
    }
}
