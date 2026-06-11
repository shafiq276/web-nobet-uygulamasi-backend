<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // Çerezden token al
            $token = $request->cookie('jwt'); // Çerez adı 'jwt_token'

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 417);
            }

            // Token'i JWTAuth'e geç
            $user = JWTAuth::setToken($token)->authenticate();
        } catch (TokenExpiredException $e) {
            // Token süresi dolmuşsa
            return response()->json(['error' => 'Token has expired'], 407);
        } catch (TokenInvalidException $e) {
            // Token geçersizse
            return response()->json(['error' => 'Token is invalid'], 412);
        } catch (JWTException $e) {
            // Genel bir hata oluşursa
            return response()->json(['error' => 'Token not provided'], 417);
        }

        // İstek doğrulandı, bir sonraki işleme geç
        return $next($request);
    }
}
