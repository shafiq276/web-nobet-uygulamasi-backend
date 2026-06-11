<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkCreatorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        // Kullanıcının giriş yapıp yapmadığını kontrol et
        if (auth()->check() &&  auth()->user()->role === 'creator') {
            return $next($request); // Admin ise devam et
        }

        // Yetkisiz kullanıcılar için geri dönüş
        return response()->json(['message' => 'Unauthorized. creator only.'], 424);

    }
}
