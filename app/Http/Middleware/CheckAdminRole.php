<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kullanıcının giriş yapıp yapmadığını kontrol et
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'creator')) {
            return $next($request); // Admin ise devam et
        }

        // Yetkisiz kullanıcılar için geri dönüş
        return response()->json(['message' => 'Unauthorized. Admins only.'], 423);
    }
}
